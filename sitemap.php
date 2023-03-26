<?php
header('Content-type: application/xml');
echo '<?xml version="1.0" encoding="UTF-8"?>';
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors',1);
set_include_path('/home/ifqesche/public_html');
require_once 'system/define.php';
require_once INCLUDEPATH.'/system/Application.php';
require_once INCLUDEPATH.'/system/Router.php';
require_once INCLUDEPATH.'/system/View.php';
require_once INCLUDEPATH.'/system/Controller.php';
require_once INCLUDEPATH.'/system/Database.php';
require_once INCLUDEPATH.'/system/Model.php';
require_once INCLUDEPATH.'/system/functions.php';
require_once INCLUDEPATH.'/app/controllers/AuthController.php';

require_once INCLUDEPATH.'/app/models/KategorilerModel.php';
require_once INCLUDEPATH.'/app/models/SubjectModel.php';
require_once INCLUDEPATH.'/app/models/UserModel.php';

use App\Controllers\Subject;
use App\Models\SubjectModel;
use App\System\Application;
$app = new Application();

$subjectModel = new SubjectModel();
$subjectList = $subjectModel->getAllSubject();

$maxdate = max(array_map(function($subjectList)  { return $subjectList['eklemezamani']; }, $subjectList));
function sidebarCreate($ustId){
    $menuler = Application::$app->kategoriler->get($ustId);
    foreach ($menuler as $menu) {
        $subMenu = Application::$app->kategoriler->get($menu["ID"]);
        if (count($subMenu)>0) {
            echo "<url>
                    <loc>https://eschelping.com/kategori/".$menu["link"]."</loc>
                    <lastmod>".(new \Datetime($menu["EKLEMEZAMANI"]))->format('c')."</lastmod>
                 </url>";
                    sidebarCreate($menu["ID"]);
        }else{
            echo "<url>
                    <loc>https://eschelping.com/kategori/".$menu["link"]."</loc>
                    <lastmod>".(new \Datetime($menu["EKLEMEZAMANI"]))->format('c')."</lastmod>
                 </url>";
        }
    }
}
?>
<urlset
      xmlns="https://www.sitemaps.org/schemas/sitemap/0.9"
      xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
      xsi:schemaLocation="http://www.sitemaps.org/schemas/sitemap/0.9
            https://www.sitemaps.org/schemas/sitemap/0.9/sitemap.xsd">
    <url>
    <loc>https://eschelping.com/</loc>
    <lastmod><?php echo (new \Datetime($maxdate))->format('c'); ?></lastmod>
    </url>

    <?php sidebarCreate(2);?>

    <?php foreach ($subjectList as $subject): ?>
    <url>
    <loc>https://eschelping.com/subject/<?php echo $subject["url"]; ?></loc>
    <lastmod><?php echo (new \Datetime($subject["eklemezamani"]))->format('c'); ?></lastmod>
    </url>
    <?php endforeach; ?>
    
</urlset>