<?php
namespace App\Controllers;

use App\Models\Image;
use App\Models\SubjectModel;

use App\Models\User;

use App\System\Controller;

use App\System\Application;

// require_once INCLUDEPATH.'/vendor/autoload.php';





class Auth extends Controller

{

    public function userProfileIndex($values = null)

    {
        if(!$this->checkLogin())
        {
            header('Location: '.SITEADRESS);
            exit;

        }

        $userid = $_SESSION["ESCLOGIN"]["USERID"];
        $user = new User();

        $user->id = $userid;
        $model = $user->get();


       return $this->render('userprofile',['model'=>$model]);

    }

    public function resetPassword()
    {
        if($this->checkLogin())
        {
      
            $model = new User();
            $model->id = $_SESSION["ESCLOGIN"]["USERID"];
            if ($_POST) {//password_hash(md5($this->password), PASSWORD_DEFAULT)
                $model->password =password_hash(md5($_POST["password"]),PASSWORD_DEFAULT);
                $model->repassword = md5($_POST["repassword"]);
                if(password_verify($model->repassword,$model->password)){
                    $model->resetPass();
                    $model = [
                        'header'=>'Şifreniz Sıfırlandı',
                        'content'=> 'Şifrenizi başarılı bir şekilde sıfırladınız. Artık yeni şifrenizi kullanmaya başlayabilirsiniz.',
                        'link'=>'/',
                        'linktitle'=>'Anasayfaya dön'
                    ];
                    return $this->render("confirmation",['model'=>$model]);
                }else {
                    return $this->render('resetpassword',['hata'=>"Parolalar eşleşmiyor"]);
                }
            }    
        }

        $this->render('resetpassword',[]);
    }

    public function userSettingsIndex()

    {
        $userid = $_SESSION["ESCLOGIN"]["USERID"];
        $user = new User();
        $user->id = $userid;
        if ($_POST && $this->checkLogin()) {

            $user->loadData($_POST);
            $user->update();
        }
        
        
        $model = $user->get();

        Application::$app->view->addPartialCSS(["jquery.modal.min.css","fontawesome/css/all.css"]);

        Application::$app->view->addPartialScripts(["jquery.modal.min.js"],'$("#resetpassword").click(function(event){

            event.preventDefault();

            $("#resetpasswordModal").modal({

               escapeClose: true,

               clickClose: true,

               showClose: true,

               fadeDuration: 100,

               fadeDelay: 0.50,

               closeClass: \'icon-remove\',

               closeText: \'<i class="fa fa-window-close"></i>\',

               backdrop: false, keyboard: false

            });

         });');

        return $this->render('usersettings',['model'=>$model]);

    }

    public function updateImage()
    {
        if (!$this->checkLogin()) {
            return;
        }

        $userid = $_SESSION["ESCLOGIN"]["USERID"];
        $image = new Image();
        $image->elementtypeno = 2;
        $image->elementtypeid = $userid;
        
        $result = $image->getFromElementId($image->elementtypeno,$image->elementtypeid);
        

        $tmpname = $_FILES["profileimage"]['tmp_name'];
        $name = $_FILES["profileimage"]['name'];
        $uploadPath = "assets/images/profile/";
        $image->path = $name;
        if (move_uploaded_file($tmpname,$uploadPath.$name)) {
            if (count($result)>0) 
            {
                $image->id = $result[0]["id"];
                $image->update();
            }else {
                $image->dimensions = 1;
                $image->insert();
            }
        }
       header("Location: ".SITEADRESS."userprofile");

    }

    public function signUpIndex($values = null)

    {
        Application::$app->view->title = "Küçükaslan Matbaa Üye Ol";

        Application::$app->view->description = "Üyelik Sayfası";

        $userModel = new User();

        

        if ($_POST) {

            if (!isset($_POST["contractvalid"])) {

                $_POST["contractvalid"]="";

            }

            $userModel->loadData($_POST);

            if ($userModel->validate() && $userModel->createUser($_POST)) {

                return $this->renderViewContent("Başarılı bir şekilde üye oldunuz. Aktif etmek için lütfen mailinizde gelen linke tıklayınız.");
                // return $this->render('signup',['model'=>$userModel,'success'=>"true"]);
            }

            

            return $this->render('signup',['model'=>$userModel,'error'=>$userModel->getError(),'errorMessage'=>$userModel->getErrorMessage()]);

        }

        return $this->render('signup',['model'=>$userModel]);

    }

    


    public function mailonay($onayKod)

    {

        $userModel = new User();

        $userModel->mailOnay = $onayKod;

        if ($userModel->mailOnay($onayKod)) {

            $this->renderViewContent('

                 <div class="container my-5">

                    <div class="row  align-items-center">

                        <div class="col-md-12 col-lg-7 text-center text-lg-left bg-success text-white">

                            Mail adresiniz başarılı bir şekilde doğrulandı. <br> Üye girişi yapabilirsiniz.

                            <a href="' . SITEADRESS . '" class="text-white">Anasayfaya dön</a>

                        </div>

                    </div>

                 </div>

                 ');

        }

        else {

            $this->renderViewContent('

                 <div class="container my-5">

                    <div class="row  align-items-center">

                        <div class="col-md-12 col-lg-7 text-center text-lg-left bg-danger text-white">

                            Mail Onaylamada hata <br>

                            <a href="' . SITEADRESS . '" class="text-white">Anasayfaya dön</a>

                        </div>

                    </div>

                 </div>

                 ');

        }

    }


    public function glogin()

    {

        if($this->checkLogin())

        {

            header('Location: '.SITEADRESS);

            exit;

        }



        $client = new \Google_Client();

        $client->setClientId("378990307480-ve8vrs4ud7c96qr5ntk2glnegjoll0sf.apps.googleusercontent.com");

        $client->setClientSecret("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");

        $client->setRedirectUri("https://eschelping.com/glogin");

        $client->addScope("email");

        $client->addScope("profile");



        if (isset($_GET["code"])) {

            $token = $client->fetchAccessTokenWithAuthCode($_GET['code']);


                $client->setAccessToken($token);

                $gauth = new \Google_Service_Oauth2($client);

                $google_info = $gauth->userinfo->get();

                echo "<pre>";

                print_r($google_info);

                echo "</pre>";


        }else {

            echo "<a href='".$client->createAuthUrl()."'> Login google </a>";

        }

        

    }

    public function login()
    {
        if($this->checkLogin())
        {

            header('Location: '.SITEADRESS);

            exit;

        }


        Application::$app->view->title = "Küçükaslan Matbaası | Üye Giriş";

        Application::$app->view->description = "Üye Giriş Sayfası";

        

        $user = new User();  

        

        if ($_POST) {
        

            if (!isset($_POST["remember"])) {

                $_POST["remember"]="";

            }

            $user->loadData($_POST); 

            $user->validate();

            if (isset($_POST) && $user->login()) {

                // header(Application::$app->functions->HTTPStatus(201));

                $_SESSION["ESCLOGIN"]["firstname"] = $user->name;

                $_SESSION["ESCLOGIN"]["USERID"] = $user->userId;

                $_SESSION["ESCLOGIN"]["SESSION"] = "ACTIVE";

                $_SESSION["ESCLOGIN"]["PROFILEPHOTOURL"] = $user->pictureurl;
                $_SESSION["ESCLOGIN"]["rolid"] = $user->rolid;

                Application::$app->classList["user"] = $user;

                header('Location: '.SITEADRESS);

                echo "<b>Giriş Başarılı Anasayfaya Yönlendiriliyorsunuz...!</b>";

            }

            return $this->render('login',['model'=>$user]);

        }

        return $this->render('login',['model'=>$user]);

    }

    public function forgetPassword()
    {
        return $this->render('forgetpassword', []);
    }
    public function checkLogin()
    {
        if (!isset($_SESSION["ESCLOGIN"]) || $_SESSION["ESCLOGIN"]["SESSION"] != "ACTIVE") {

            if (isset($_COOKIE["REMEMBERAGS"]) && $_COOKIE["REMEMBERAGS"] != 'false') {

                $cooktoken = $_COOKIE["REMEMBERAGS"];

                $browser = md5($_SERVER['HTTP_USER_AGENT']);

                

                // $user = Application::$app->classList["user"];

                $user = new User();

                $result = $user->getUserRemembers($cooktoken,$browser);

                if ($result) {

                    $cookieUser = $result["userid"];

                    $checkUser = $user->getUserList($cookieUser, null);

                    

                    if ($checkUser) {

                        $user->userId = $checkUser[0]["id"];

                        $user->email = $checkUser[0]["email"];

                        $user->name = $checkUser[0]["firstname"];

                        $user->surname = $checkUser[0]["lastname"];

                        $_SESSION["ESCLOGIN"]["firstname"] = $user->name;

                        $_SESSION["ESCLOGIN"]["SESSION"] = "ACTIVE";

                        $_SESSION["ESCLOGIN"]["USERID"] = $user->userId;

                        return true;

                    }

                    else

                    {

                        setcookie("REMEMBERAGS",'false',time()-3600,'/');

                        unset(Application::$app->classList["user"]);

                        return false;

                        //header('Location:'.SITEADRESS.'login');

                    }

                }

                else

                {

                    setcookie("REMEMBERAGS",'false',time()-3600,'/');

                    //header('Location:'.SITEADRESS.'login');

                    return false;



                }

            }

            else 

            {

                setcookie("REMEMBERAGS",'false',time()-3600,'/');

                //header('Location:'.SITEADRESS.'login');

                return false;

            }

        }

        else 

        {

            return true;

        }

    }

    public function logout()

    {

        $_SESSION["ESCLOGIN"]=null;

        unset(Application::$app->classList["user"]);

        session_destroy();

        setcookie("REMEMBERAGS",'false',time()-3600,'/');

        header('Location:'.SITEADRESS.'login');

        exit;

    }



    public function userVisit()

    {

        $userModel = new User();

        $userModel->UserVisits($_POST["visitip"],$_POST["page"]);

    }



}