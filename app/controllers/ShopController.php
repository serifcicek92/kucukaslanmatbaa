<?php 
namespace App\Controllers;
use App\Models\Basket;
use App\Models\Basketdetail;
use App\Models\Category;
use app\Models\Creditcard;
use App\Models\Image;
use App\Models\Order;
use App\Models\Orderdetail;
use App\Models\Product;
use App\System\Application;
use App\System\Controller;

class Shop extends Controller{
    public function getProduct($values = null)
    {
        $sayfa = $_GET["sayfa"] ?? 1;

        $categoryMod = new Category();
        $categoryId = $categoryMod->get(null, null, $values)[0]["id"];
        $productModel = new Product();
        $productModel->categoryid = $categoryId;
        $products = $productModel->getListView($sayfa,20,$categoryId);

        
        $this->render('shop', ['model'=>$products]);
    }

    public function getProductDetail($values = null)
    {
        $productModel = new Product();
        $productModel->seourl = $values;
        $model = $productModel->getProductDetail();



        Application::$app->view->addPartialScripts([],'
            
        ');

        $this->render('productdetail', ['model' => $model[0]]);
    }

    public function userOrders()
    {
        return $this->render('userorders', []);
    }
    
    public function getCheckout($errors = [])
    {
        if (Application::$app->auth->checkLogin()) {


            $userid = $_SESSION["ESCLOGIN"]["USERID"];
            $basket = new Basket();
            $resultBasket = $basket->getUserActiveBasket($userid);
            $basket->loadData($resultBasket[0]);
            if (!isset($basket->id)) {
                return $this->renderViewContent("Sepet Boş");
            }
            $basketDetay = new Basketdetail();
            

            $basketDetay->basketid = $basket->id;
            $detayResult = $basketDetay->get();
            $model = [];
            if ($basket->id>0 && count($detayResult)>0) {
                $toplam = 0;
                foreach ($detayResult as $detay) 
                {
                    
                    $productModel = new Product();
                    $productModel->id = $detay->productid;
                    $resultProduct = $productModel->getProductDetailFromProductId();
                    $resim = explode(',',$resultProduct[0]->imagelist)[0];
                    $toplam+= $detay->miktar * $resultProduct[0]->prices;

                    $array = array('product' => $resultProduct[0],'total'=>$toplam,'basketdetay'=>$detay);
                    array_push($model,$array);
                    
                }
            }

            $arrpost = array('post'=>$_POST);
            array_push($model,$arrpost);

        }
        return $this->render('checkout',['model'=>$model,'errors'=>$errors]);
    }

    public function getBasketList(){


        if (Application::$app->auth->checkLogin()) {
            $userid = $_SESSION["ESCLOGIN"]["USERID"];
            $basket = new Basket();
            $resultBasket = $basket->getUserActiveBasket($userid);
            $basket->loadData($resultBasket[0]);
            
            $basketDetay = new Basketdetail();
            $basketDetay->basketid = $basket->id;
            $detayResult = $basketDetay->get();
            $model = [];
            if ($basket->id>0 && count($detayResult)>0) {
                $toplam = 0;
                foreach ($detayResult as $detay) 
                {
                    $productModel = new Product();
                    $productModel->id = $detay->productid;
                    $resultProduct = $productModel->getProductDetailFromProductId();
                    $resim = explode(',',$resultProduct[0]->imagelist)[0];
                    $toplam+= $detay->miktar * $resultProduct[0]->prices;

                    $array = array('product' => $resultProduct[0],'total'=>$toplam,'basketdetayid'=>$detay->id);
                    array_push($model,$array);
                    
                }
            }

        }
        
        return $this->render('sepet',['model'=>$model]);
    }
    

    public function checkoutComplate()
    {
        if ($_POST && Application::$app->auth->checkLogin()) {
            $userid = $_SESSION["ESCLOGIN"]["USERID"];
            $order = new Order();
            $order->loadData($_POST);

            $creditCard = new Creditcard();
            $creditCard->loadData($_POST);
            $validateorder = $order->validate();
            $validatecreditcard = $creditCard->validate();
            if ($validateorder && $validatecreditcard) {
                
                // Kredi kartı çekme işlemleri




                // sipariş kaydetme işlemleri
                $basket = new Basket();
                $basketRes = $basket->getUserActiveBasket($userid);
                $basket->loadData($basketRes[0]);

                $basketDetay = new Basketdetail();
                $basketDetay->basketid = $basket->id;
                $detayResult = $basketDetay->get();


                $toplam = 0;
                $taxtoplam = 0;

                $orderDetail = new Orderdetail();

                foreach ($detayResult as $detay) 
                {
                    $productModel = new Product();
                    $productModel->id = $detay->productid;
                    $resultProduct = $productModel->getProductDetailFromProductId();
                    $resim = explode(',',$resultProduct[0]->imagelist)[0];
                    $toplam+= $detay->miktar * $resultProduct[0]->prices;
                    $taxtoplam+= $toplam * 100 / $resultProduct[0]->taxtype;

                    $array = array('product' => $resultProduct[0],'total'=>$toplam,'basketdetayid'=>$detay->id);
                    array_push($model,$array);

                    $orderDetail->productid = $detay->id;
                    $orderDetail->amount = $detay->miktar;
                    // $orderDetail->unitprice = $productModel->

                    
                }

                $order = new Order();
                $order->userid = $userid;
                $order->status = 1;
                $order->paymentoption = 1;
                $order->installments = 1;
                $order->addid = $userid;
                $order->taxtotal = $taxtoplam;
                $order->amounttotal = $toplam;
                $order->orderer = $userid;




            }else{
                $this->getCheckout(array_merge($order->getErrors(),$creditCard->getErrors()));
            }

        }
    }


}