<?php

namespace App\Controllers;

use App\Models\Basket;
use App\Models\Basketdetail;
use App\Models\Product;
use App\System\Application;
use App\System\Controller;

class Main extends Controller
{
    public function avitoheader()
    {
        echo "xxxxxx";
        exit;
    }


    public function BasketView()
    {
        if (Application::$app->auth->checkLogin()) {
            $userid = $_SESSION["ESCLOGIN"]["USERID"];
            $basket = new Basket();
            $resultBasket = $basket->getUserActiveBasket($userid);
            $basket->loadData($resultBasket[0]);
            if (!isset($basket->id)) {
                return;
            }
            $basketDetay = new Basketdetail();
            $basketDetay->basketid = $basket->id;
            $detayResult = $basketDetay->get();
            if ($basket->id>0 && count($detayResult)>0) {
                $toplam = 0;
                foreach ($detayResult as $detay) 
                {
                    $productModel = new Product();
                    $productModel->id = $detay->productid;
                    $resultProduct = $productModel->getProductDetailFromProductId();
                    $resim = explode(',',$resultProduct[0]->imagelist)[0];

                    echo '<div class="media">
                            <a class="pull-left" href="#!">
                                <img class="media-object" src="assets/images/products/'.$resim.'" alt="image" />
                            </a>
                            <div class="media-body">
                                <h4 class="media-heading"><a href="product/'.$resultProduct[0]->seourl.'">'.$resultProduct[0]->name.'</a></h4>
                                <div class="cart-price">
                                    <span>'.$detay->miktar.' adet</span>
                                    <span>'.$resultProduct[0]->prices.' TL</span>
                                </div>
                                <h5><strong>'.$detay->miktar * $resultProduct[0]->prices.' TL</strong></h5>
                            </div>
                            <a href="/sepettensil/'.$detay->id.'" class="remove"><i class="tf-ion-close"></i></a>
                        </div>'; 

                    $toplam+= $detay->miktar * $resultProduct[0]->prices;

                    
                }
                echo '<div class="cart-summary">
                        <span>Toplam</span>
                        <span class="total-price">'.$toplam.' TL</span>
                    </div>';

                echo '<ul class="text-center cart-buttons">
                        <li><a href="sepet" class="btn btn-small">Sepete Git</a></li>
                        <li><a href="checkout" class="btn btn-small btn-solid-border">Siparişi Tamamla</a></li>
                    </ul>';

            }

        }
        
    }
}
