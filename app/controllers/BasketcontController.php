<?php 
namespace App\Controllers;
use App\Models\Basket;
use App\Models\Basketdetail;
use App\System\Application;
use App\System\Controller;
class Basketcont extends Controller{
    public function addBasket()
    {
        if ($_POST && isset($_POST["productid"])) {
            //sepet yoksa sepet oluştur 
            if(Application::$app->auth->checkLogin()){
                $userid = $_SESSION["ESCLOGIN"]["USERID"];
                $basketModel = new Basket();
                $basket = $basketModel->getUserActiveBasket($userid);
                if(count($basket) == 0){
                    $basketModel->userid = $userid;
                    $basketModel->addid = $userid;
                    $basketModel->guuid = uniqid();
                    $basketModel->insert();
                }else{
                    # güncel olmayanları sil
                    // $_COOKIE["BASKETUUID"] = $basket[0]->guuid;
                    $basketModel->loadData($basket[0]);
                }
                $basketDetayModel = new Basketdetail();
                $basketDetayModel->basketid = $basketModel->id;
                $basketDetayModel->productid = $_POST["productid"];

                $basketDetayGet = $basketDetayModel->get();
                if (count($basketDetayGet)>0) {
                    $basketDetayModel->id = $basketDetayGet[0]->id;
                    $basketDetayModel->miktar = $basketDetayGet[0]->miktar+1;
                    $basketDetayModel->updateid = $userid;
                    echo $basketDetayModel->update();
                }else{
                    $basketDetayModel->addid = $userid;
                    $basketDetayModel->miktar = 1;
                    echo $basketDetayModel->insert();
                }
            }else {
                //httprefer ekle üye olsun
            }            

        }
        
    }

    public function delBasket($detailId)
    {
        if ($detailId!=null) {
            $basketDetailModel = new Basketdetail();
            $basketDetailModel->id = $detailId;
            $basketDetailModel->delete();
        }
        header("location:".$_SERVER['HTTP_REFERER']);
        exit;

    }

    public function FunctionName()
    {
        echo "testt";
    }
    
    
}