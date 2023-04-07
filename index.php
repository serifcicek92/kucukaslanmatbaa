<?php

// php -S localhost:8888
// echo phpinfo();
// exit;
error_reporting(E_ALL);
error_reporting(1);

ini_set('display_errors', 1);

ini_set('display_startup_errors',1);

ob_start();

session_start();


//set include path required for yor server
// set_include_path('C:\xampp\htdocs\kucukaslanmatbaa');

set_include_path('/home/kucukas1/public_html');


require_once 'system/define.php';

require_once 'plugins/phpexcel/PHPExcel.php';


// Include System Files
foreach (glob(INCLUDEPATH."/system/*.php") as $filename) {
    require_once $filename;
}

// Include Models 
foreach (glob(INCLUDEPATH."/app/models/*.php") as $filename) {
    require_once $filename;
}

// Include auth
require_once INCLUDEPATH.'/app/controllers/AuthController.php';


use App\System\Application;


$app = new Application();


$app->run("/","anasayfa/index");
$app->run("/anasayfa","anasayfa/index");
$app->run("/shop/{url}","shop/getProduct");
$app->run("/product/{url}", "shop/getProductDetail");
$app->run("/hakkimizda", "anasayfa/hakkimizda");
$app->run("/iletisim", "anasayfa/iletisim");
$app->run("/firmamenu/{val}","anasayfa/firmaMenu");


$app->run("/userprofile","auth/userProfileIndex");
$app->run("/usersettings","auth/userSettingsIndex");
$app->run("/usersettings","auth/userSettingsIndex","post");
$app->run("/updateimage","auth/updateImage","post");
$app->run("/userorders", "shop/userOrders");
$app->run("/order/{id}","shop/orderDetail" );
$app->run("/resetpassword","auth/resetPassword");
$app->run("/resetpassword","auth/resetPassword","post");


$app->run("/login","auth/login");
$app->run("/login","auth/login","post");


$app->run("/signup","auth/signUpIndex");
$app->run("/signup","auth/signUpIndex","post");
$app->run("/forgetpassword","auth/forgetPassword");
$app->run("/forgetpassword","auth/forgetPassword","post");
$app->run("/logout","auth/logout");
$app->run("/mailonay/{val}","auth/mailonay");

$app->run("/checkout","shop/getCheckout");
$app->run("/sepet","shop/getBasketList");
$app->run("/sepettensil/{id}","basketcont/delBasket");
$app->run("/checkoutcomplate","shop/checkoutComplate","post");


$app->run("/addbasket","basketcont/addBasket","post");
// $app->run('/user/uservisit','auth/userVisit','post');
$app->run("/test","basketcont/FunctionName");
$app->run("/basketview","main/BasketView","post");

$app->run("/admin", "admin/index");
$app->run("/admin/productedit/{id}", "admin/productedit");
$app->run("/admin/productedit", "admin/productnew");
$app->run("/admin/productsave", "admin/productSave", "post");
$app->run("/admin/productsave/{id}", "admin/productSaveId", "post");
$app->run("/admin/productimageupload", "admin/productImageUpload","post");
$app->run("/admin/productlist", "admin/productList");
$app->run("/admin/productlist/{id}", "admin/productListPage");
$app->run("/admin/productdelete/{id}", "admin/productDelete");
$app->run("/admin/orderlist", "admin/orderlist");
$app->run("/excel/export", "excel/exportExcel");
$app->run("/excel/import", "excel/importProductListExcel","post");
$app->run("/admin/firmamenu","admin/firmaMenu");
$app->run("/admin/firmamenu","admin/firmaMenu","post");
$app->run("/admin/firmamenu/{id}","admin/firmaMenu","post");
$app->run("/admin/firmamenuedit/{id}","admin/firmaMenuEdit");
$app->run("/admin/firmamenusil/{id}","admin/firmaMenuSil");




ob_end_flush();

