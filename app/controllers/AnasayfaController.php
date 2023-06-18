<?php

namespace App\Controllers;



use App\Models\Product;
use App\Models\SubjectModel;

use App\System\Controller;

use App\System\Application;



class anasayfa extends Controller

{

    public function index()

    {

        Application::$app->view->title = "Küçükaslan Matbaa | Davetiye | Kartvizit | Dijital Baskı";

        Application::$app->view->description = "Davetiye tasarımları, kartvizitler ve baskı işlemleri";

        // $subjectModel = new SubjectModel();

        //$model = $subjectModel->getNewsSubject();

        // Application::$app->view->addPartialScripts(["test.js","x.js","y.js"],"inser iinto test");

        // Application::$app->view->addPartialCSS(["1.css","2.css","3.css"],"a{display:block;}");

        $productModel = new Product();
        $product = $productModel->getList(1,3);


        $this->render('home',['model'=>$product]);

    }

    public function privact()

    {

        $this->renderViewContent("

            <p>Daha iyi hizmet sunmak için çerezleri kullanmaktayız siteyi ziyaret ederek doğacak yükümlşülüklerden sorumlu bulunmaktasınız.</p>

            <p>Üye olarak üyelikte girdiğiniz verileri saklamayı kabul ettiğinizi, yayınladığınız içeriklerden sorumlu olduğunuzu kabul etmiş olursunuz.</p>

        ");

    }

    public function iletisim()
    {
        return $this->render('iletisim', []);
    }

    public function hakkimizda()
    {
        return $this->render('hakkimizda', []);
    }

    public function firmaMenu($sayfa)
    {

        $file = INCLUDEPATH.'/assets/files/pdf/'.$sayfa.'.pdf';
        if (!file_exists($file)) {
            # code...
            die("dosya yok ".$file);
        }
        $filename = $sayfa.'.pdf';
        header('Content-type: application/pdf');
        header('Content-Disposition: inline; filename="' . $filename . '"');
        header('Content-Transfer-Encoding: binary');
        header('Content-Length: ' . filesize($file));
        header('Accept-Ranges: bytes');
        return @readfile($file);

    }
}