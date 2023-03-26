<?php

namespace App\Controllers;

use App\Models\Category;
use App\Models\Combovalues;
use App\Models\FirmaMenu;
use App\Models\Image;
use App\Models\Product;
use App\Models\ProductPrices;
use App\System\Application;
use App\System\Controller;

class Admin extends Controller
{

    public Product $product;
    function __construct()
    {
        if(!Application::$app->auth->checkLogin()){
            header("Location: /login");
            exit;
        }
        $this->product = new Product();
    }

    public function index()
    {
        $this->renderAdmin('home', []);
    }

    public function productedit(?int $id)
    {
        $guuid = null;
        $_SESSION["PRODUCTGUUID"] = $_SESSION["PRODUCTGUUID"] ?? uniqid();
        $guuid = $_SESSION["PRODUCTGUUID"];


        $categoryModel = new Category();
        $categories = $categoryModel->get();


        $comboValuesModel = new Combovalues();
        $taxTypes = $comboValuesModel->get('TAXTYPE');

        $Image = new Image();
        $images = $Image->getFromGuuid($guuid);

        if (isset($id)) {
            $imagesFromId = $Image->getFromElementId(1, $id);
            for ($i = 0; $i < count($imagesFromId); $i++) {
                array_push($images, $imagesFromId[$i]);
            }

            $productPrice = new ProductPrices();
            $productPrice->productid = @$id;
            $price = $productPrice->getEndPrices();

            $product = new Product();
            $product->id = @$id;
            $product->prices = @$price[0]["prices"];
            $product->taxtype = @$price[0]["taxtype"];
            $product->loadData($product->get()[0]);
            $this->product = $product;
            $model = $product;
        } else {
            $model = $this->product;
        }







        Application::$app->view->addPluginScripts(["plugins/summernote/js/summernote.min.js", "scripts/plugins-init/summernote-init.js"]);
        Application::$app->view->addPartialScripts(
            [],
            'const form = document.querySelector("#form");form.addEventListener("submit", function(event) {event.preventDefault();let a = SendXMLHttpRequest(new FormData(form),"' . (!isset($product->id) ? "admin/productsave" : "admin/productsave/" . $product->id) . '",function handle(returnStr, status) {console.log(returnStr);window.location.href="admin/productedit/"+returnStr.trim();document.querySelector("#donen").innerHTML = returnStr;});});'
        );
        $this->renderAdmin('productedit', ['categories' => $categories, 'taxTypes' => $taxTypes, 'images' => $images, 'model' => $model]);
    }

    public function productnew()
    {
        $this->productedit(null);
    }

    public function productSaveId(?int $id)
    {
        if (isset($id)) {
            // update
            $this->product->id = $id;
            $this->product->loadData($_POST);
            $this->product->seourl = Application::$app->functions->seoUrlOlustur($this->product->name);
            $this->product->updateid = 1;
            $this->product->update();

            //price insert
            $price = new ProductPrices();
            $price->loadData($_POST);
            $price->productid = $this->product->id;
            $price->prices = $this->product->prices;
            $price->taxtype = $this->product->taxtype;
            $price->addid = 1;
            $price->insert();
            //price update son fiyatı bul varsa update yoksa insert
            // $price = new ProductPrices();
            // $price->loadData($_POST);
            // $price->productid = $this->product->id;
            // $price->updateid = 1;
            // $price->update();

            if (isset($_SESSION["PRODUCTGUUID"])) {
                $image = new Image();
                $image->guuid = $_SESSION["PRODUCTGUUID"];
                $image->elementtypeid = $this->product->id;
                $image->updateProductIdFroumGuuid();
            }
            unset($_SESSION["PRODUCTGUUID"]);
        } else {
            //insert
            $this->product->loadData($_POST);
            $this->product->seourl = Application::$app->functions->seoUrlOlustur($this->product->name);
            $this->product->addid = 1;
            $this->product->insert();

            //price insert
            $price = new ProductPrices();
            $price->loadData($_POST);
            $price->productid = $this->product->id;
            $price->prices = $this->product->prices;
            $price->taxtype = $this->product->taxtype;
            $price->addid = 1;
            $price->insert();

            if (isset($_SESSION["PRODUCTGUUID"])) {
                $image = new Image();
                $image->guuid = $_SESSION["PRODUCTGUUID"];
                $image->elementtypeid = $this->product->id;
                $image->updateProductIdFroumGuuid();
            }
            unset($_SESSION["PRODUCTGUUID"]);
        }



        //         Array
        // (
        //     [name] => başlık
        //     [categoryid] => 1
        //     [code] => k58945
        //     [taxtype] => 1
        //     [prices] => 55.56
        //     [content] => <p>fahafasd fasd fa</p><p>sdf</p><p>as</p><p>dfa</p>
        // )
        echo $this->product->id;
    }

    public function productSave()
    {
        $this->productSaveId(null);
    }

    public function productDelete($id)
    {
        if (isset($id)) {
            $product = new Product();
            $product->id = $id;
            $product->delete();
            if (isset($_SERVER['HTTP_REFERER'])) {
                header('Location: ' . $_SERVER['HTTP_REFERER']);
                exit;
            }else {
                header('Location: ' . '/admin/productlist');
            }
            exit;
        } else {
            if (isset($_SERVER['HTTP_REFERER'])) {
                header('Location: ' . $_SERVER['HTTP_REFERER']);
                exit;
            }else {
                header('Location: ' . '/');
            }
        }
    }
    public function productImageUpload()
    {
        $listImage = array();
        $files = array_filter($_FILES['file']['name']);
        $total_count = count($_FILES['file']['name']);
        for ($i = 0; $i < $total_count; $i++) {
            //The temp file path is obtained
            $tmpFilePath = $_FILES['file']['tmp_name'][$i];
            $item = [
                'tmp_name' => $_FILES['file']['tmp_name'][$i],
                'name' => $_FILES['file']['name'][$i]
            ];

            $image = new Image();
            $image->addid = 1;
            $image->guuid = $_SESSION["PRODUCTGUUID"];
            $image->dimensions = 1;
            $image->visibleorder = $i;
            $image->title = "";
            $image->addid = 1;
            $image->elementtypeno = 1;
            $image->saveImage($item);
            $listImage[] = $image;
        }

        echo json_encode($listImage);
    }

    public function productListPage(?int $page)
    {
        $prodcutModel = new Product();
        $model = $prodcutModel->getList($page ?? 1, 50);
        $this->renderAdmin("productlist", ['model' => $model]);
    }
    public function productList()
    {
        $this->productListPage(null);
    }

    public function orderList()
    {
        // Application::$app->view->addPartialCSS(["jquery.dataTables.min.css"], null);
        // Application::$app->view->addPartialScripts([
        //     "jquery.dataTables.min.js"
        // ], 
        // "<script>
        // (function($) {
         
        //     var table = $('#example5').DataTable({
        //         searching: false,
        //         paging:true,
        //         select: false,
        //         //info: false,         
        //         lengthChange:false 
                
        //     });
        //     $('#example tbody').on('click', 'tr', function () {
        //         var data = table.row( this ).data();
                
        //     });
           
        // })(jQuery);
        // </script>");


        
        return $this->renderAdmin("orderlist", []);
    }

    public function firmaMenu($id = null)
    {
        $firmamenu = new FirmaMenu();
        if ($_POST) {
            
            $name = $_POST["linki"].".pdf";
            $firmamenu->firmaadi = $_POST["firmaadi"];
            $firmamenu->linki = $_POST["linki"];
            
            if (isset($_FILES["dosyayolu"])) {
                
                $firmamenu->dosyayolu = "assets/files/pdf/".$name;
                $tmpname = $_FILES["dosyayolu"]["tmp_name"];
               
                move_uploaded_file($tmpname,"assets/files/pdf/".$name);
            }


            if ($id == null && isset($_FILES["dosyayolu"])) {
                $firmamenu->insert();
            }else {
                $firmamenu->id = $id;
                $firmamenu->update();
                
                return $this->renderAdmin('firmamenu',['model'=>$firmamenu->get()]);
                
            }

            
        }
        $model = $firmamenu->getList();
       return $this->renderAdmin('firmamenu',['model'=>$model]);
    
    }


    public function firmaMenuEdit($id)
    {
        $firmamenu = new FirmaMenu();
        $firmamenu->id = $id;
        
        $this->renderAdmin('firmamenu',['model'=>$firmamenu->get()]);
    }
    public function firmaMenuSil($id)
    {
        $firmamenu = new FirmaMenu();
        $firmamenu->id = $id;
        $firmamenu->del();
    
        return $this->firmaMenu();
    }
}
