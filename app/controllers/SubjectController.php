<?php

namespace App\Controllers;



use App\Models\Kategoriler;

use App\System\Controller;

use App\System\Application;

use App\Models\SubjectModel;

use App\Models\User;



class Subject extends Controller

{

    public function kategoriList($url)

    {

        $sayfa = $_GET["sayfa"] ?? 1;

        $subjectModel = new SubjectModel();

        $subjects = $subjectModel->getSubjectList($url,($sayfa-1)*15);

        $totalSubject = $subjectModel->count($url);

        $sayfaSayisi = ceil($totalSubject->count/15);

        // var_dump($url);

        // var_dump($subjects);

        $kategorimodel = new Kategoriler();

        $kategori = $kategorimodel->get(null,$url);

        Application::$app->view->title = $kategori[0]["ADI"]." Kategorisindeki Konular";

        Application::$app->view->description = $kategori[0]["ACIKLAMA"]." kategorisinde açılan konu ve sorular.";



        $this->render('category',['url'=>$url,'model'=>$subjects,'sayfasayisi'=>$sayfaSayisi]);

    }

    public function detailIndex($url)

    {

        $subjectModel = new SubjectModel();

        $subject = $subjectModel->get($url);

        $kategori = new Kategoriler();

        $parentList = $kategori->getParentTreeList($subject->categoriid);

        Application::$app->view->title = $subject->title;

        Application::$app->view->description = $subject->subtitle." başlığındaki yazı ve yanıtlar";



    Application::$app->view->addPluginCSS(['assets/fontawesome/css/all.css',

        "//netdna.bootstrapcdn.com/font-awesome/3.0.2/css/font-awesome.css"/*,"plugins/highlight/styles/default.min.css"*/,

        "https://cdnjs.cloudflare.com/ajax/libs/highlight.js/11.6.0/styles/atom-one-dark.min.css"]);

        Application::$app->view->addPartialCSS(["bootstrap-select.css"],"");

        Application::$app->view->addPartialScripts(["ace-builds/src-noconflict/ace.js",

            "showdown.min.js",

            //"prism.js",

            "bootstrap-select.js",

            "jquery.hotkeys.js",

            "google-code-prettify/prettify.js",

            "bootstrap-wysiwyg.js",

            "wysiwyg.js"],

            "$('.reply').on('show.bs.modal', function (event) {



                var button = $(event.relatedTarget); // Button that triggered the modal

                var recipient = button.data('whatever'); // Extract info from data-* attributes

                // If necessary, you could initiate an AJAX request here (and then do the updating in a callback).

                // Update the modal's content. We'll use jQuery here, but you could use a data binding library or other methods instead.

                var modal = $(this);

                modal.find('.modal-title').text('New message to ' + recipient);

                modal.find('.modal-body input').val(recipient);

              });

              

                $('.reply').click(function (event) {

                  $('#modalnamesurname').text($(this).closest('.card-body').find('.row:first').find('.namesurmane').text()+'\'e yanıt veriniz.');

                  $('#commentref').val($(this).data('commentref'));

                  console.log($(this).data('commentref'));

                });

                

                

              ");

              Application::$app->view->addPluginScripts(["https://cdnjs.cloudflare.com/ajax/libs/highlight.js/11.6.0/highlight.min.js"],

            "document.querySelectorAll('code').forEach(el => {

                // then highlight each

                hljs.highlightElement(el);

            });

            //hljs.highlightAll();"

                );

       /* Application::$app->view->addPluginScripts(["plugins/highlight/highlight.min.js"],"

        document.querySelectorAll('code').forEach(el => {

            // then highlight each

            hljs.highlightElement(el);

        });");*/

                    

        if ($_POST && Application::$app->auth->checkLogin()) {

            $subjectModel->insertComments(

                [

                "commentref"=>$_POST["commentref"] ?? 0,

                "subjectid"=>$subject->ID,

                "content"=>$_POST["content"],

                "ekleyenid"=>$_SESSION["ESCLOGIN"]["USERID"]

                ]

            );

        }

        $sayfa = $_GET["sayfa"] ?? 1;

        $comments = $subjectModel->getComments($subject->ID,($sayfa-1)*20);

        $totalComment = $subjectModel->count_comment($subject->ID);

        $sayfaSayisi = ceil($totalComment->count/20);

        return $this->render('detail',['model'=>$subject,'parentList'=>$parentList,'comments'=>$comments,'sayfasayisi'=>$sayfaSayisi]);

    }

    public function newSubject($url = null){

        // var_dump($_POST);

        if (!Application::$app->auth->checkLogin()) {

            header("location: https://eschelping.com/");

        }

        Application::$app->view->addPluginCSS(['assets/fontawesome/css/all.css',

            /*"http://netdna.bootstrapcdn.com/twitter-bootstrap/2.3.1/css/bootstrap-combined.no-icons.min.css",

            "http://netdna.bootstrapcdn.com/twitter-bootstrap/2.3.1/css/bootstrap-responsive.min.css",*/

            "//netdna.bootstrapcdn.com/font-awesome/3.0.2/css/font-awesome.css"]);

        // Application::$app->view->addPluginScripts(["plugins/codeparl-bootstrap-markdown-editor.js"]);

        // Application::$app->view->pluginScripts.='<script>$(\'.editor\').codeparlMarkdown({fullscreen: true,help: {show: true},content: {image: {backendScript: "image-upload.php"}}});</script>';

        Application::$app->view->addPartialCSS(["bootstrap-select.css"],"");

        Application::$app->view->addPartialScripts(["ace-builds/src-noconflict/ace.js",

            "showdown.min.js",

            "prism.js",

            "bootstrap-select.js",

            "jquery.hotkeys.js",

            "google-code-prettify/prettify.js",

            "bootstrap-wysiwyg.js",

            "wysiwyg.js"]);



        $kategoriler = new Kategoriler();

        $result = $kategoriler->getChildrenTreeList(2);

        $subject = new SubjectModel();

        if ($_POST && Application::$app->auth->checkLogin()) {

            $subject->loadData($_POST);

            if ($subject->validate()) {

                $subject->content=(preg_replace('/<a(.*)>/U', '<a$1 rel="nofollow">', $subject->content));

                $output_array = [];

                $subject->content = str_replace("@{{{","<code>", $subject->content);

                $subject->content = str_replace("}}}@","</code>",$subject->content);

                // preg_match_all('/\@\{\{\{(.*)\}\}\}\@/U',  $subject->content, $output_array);

                // echo "<pre>";

                // print_r($output_array);

                // echo "</pre>";

                // exit;

                // ini_set("highlight.comment", "#008000");

                // ini_set("highlight.default", "#000000");

                // ini_set("highlight.html", "#808080");

                // ini_set("highlight.keyword", "#0000BB; font-weight: bold");

                // ini_set("highlight.string", "#DD0000");

                // if (!is_array($output_array[0])) {

                //     $subject->content = str_replace($output_array[0],highlight_string($output_array[1],true),$subject->content);

                // }

                // else if (count($output_array)>0) {

                //     for ($i = 0; $i < count($output_array[1]); $i++) {

                //         //echo highlight_string($output_array[1][$i],true)."\n";

                //         $subject->content = str_replace($output_array[0][$i],highlight_string($output_array[1][$i],true),$subject->content);

                //    }

                // }

            //    echo $subject->content;exit;

                // var_dump($output_array);

                // for ($i=0; $i < count($output_array); $i++) { 

                // $subject->content = str_replace($output_array[0],highlight_string($output_array[1]),$subject->content);

                // }

                // foreach ($output_array as $optarr) {

                //     $search =$optarr[];

                //     $replstr =  

                //     $subject->content = preg_replace('/\{\{\{(.*)\}\}\}/', highlight_string('$0'), $subject->content);

                // }

                // $subject->content = str_replace("<code>","<pre><code>",$subject->content);

                // $subject->content = str_replace("</code>","</pre></code>",$subject->content);

                // $subject->content = str_replace(["@{{{","}}}@"],["<code>","</code>"],$subject->content);

                



                $subject->insert();

                Application::$app->functions->sendMail("info@eschelping.com","Konu Açma Talebi","Yeni Konu Açma Talebi <br>".$subject->title,"");

                header("location: ".SITEADRESS."subject/".$subject->url);

                return $this->render('newsubject',['model'=>$subject,'kategoriler'=>$result,'url'=>$url]);       

            }

            $this->render('newsubject',['model'=>$subject,'kategoriler'=>$result,'url'=>$url]);

        }

        return $this->render('newsubject',['model'=>$subject,'kategoriler'=>$result,'url'=>$url]);

    }



    public function aktif($url)

    {

        

        if(!Application::$app->auth->checkLogin())

        {

            header('Location: '.SITEADRESS);

            exit;

        }

        $userid = $_SESSION["ESCLOGIN"]["USERID"];

        $subjectModel = new SubjectModel();

        $subjectModel->aktif($url,$userid);

        echo $subjectModel->getErrorMessage();

        header('Location: '.$_SERVER['HTTP_REFERER']);

    }

    public function pasif($url)

    {

        

        if(!Application::$app->auth->checkLogin())

        {

            header('Location: '.SITEADRESS);

            exit;

        }

        $userid = $_SESSION["ESCLOGIN"]["USERID"];

        $subjectModel = new SubjectModel();

        $subjectModel->pasif($url,$userid);

        // header("Location: javascript:history.go(-1)");

        header('Location: '.$_SERVER['HTTP_REFERER']);

    }

    public function del($url)

    {

        

        if(!Application::$app->auth->checkLogin())

        {

            header('Location: '.SITEADRESS);

            exit;

        }

        $userid = $_SESSION["ESCLOGIN"]["USERID"];

        $subjectModel = new SubjectModel();

        $subjectModel->del($url,$userid);

        header('Location: '.$_SERVER['HTTP_REFERER']);

    }



    public function editSubjectIndex($url = null){

        // var_dump($url);

         

         

        if (!Application::$app->auth->checkLogin() && !isset($_GET["token"]) && base64_encode(($_SESSION["ESCLOGIN"]["USERID"]*99)."editleme") != $_GET["token"]) {

            header("location: https://eschelping.com/");

        }

        Application::$app->view->addPluginCSS(['assets/fontawesome/css/all.css',

            "//netdna.bootstrapcdn.com/font-awesome/3.0.2/css/font-awesome.css"]);

         Application::$app->view->addPartialCSS(["bootstrap-select.css"],"");

        Application::$app->view->addPartialScripts(["ace-builds/src-noconflict/ace.js",

            "showdown.min.js",

            "prism.js",

            "bootstrap-select.js",

            "jquery.hotkeys.js",

            "google-code-prettify/prettify.js",

            "bootstrap-wysiwyg.js",

            "wysiwyg.js"]);



        $kategoriler = new Kategoriler();

        $result = $kategoriler->getChildrenTreeList(2);

        $subject = new SubjectModel();

        if ($_POST && Application::$app->auth->checkLogin()) {

            

            $subject->loadData($_POST);

            if ($subject->validate()) {

                

                // $subject->content=preg_replace('/<a(.*)>/U', '<a$1 rel="nofollow">', $subject->content);

                // $output_array = [];

                // preg_match_all('/\{\{\{(.*)\}\}\}/U',  $subject->content, $output_array);

                // if (!is_array($output_array[0])) {

                //     $subject->content = str_replace($output_array[0],highlight_string($output_array[1],true),$subject->content);

                // }

                // else if (count($output_array)>0) {

                //     for ($i = 0; $i < count($output_array[1]); $i++) {

                //         //echo highlight_string($output_array[1][$i],true)."\n";

                //         $subject->content = str_replace($output_array[0][$i],highlight_string($output_array[1][$i],true),$subject->content);

                //    }

                // }

                $subject->content=(preg_replace('/<a(.*)>/U', '<a$1 rel="nofollow">', $subject->content));

                // $subject->content=htmlspecialchars(preg_replace('/<a(.*)>/U', '<a$1 rel="nofollow">', $subject->content));

                $output_array = [];

                $subject->content = str_replace("@{{{","<code>", $subject->content);

                $subject->content = str_replace("}}}@","</code>",$subject->content);

                

                $subject->update($url);

                Application::$app->functions->sendMail("info@eschelping.com","Güncellenen Konu","Konu Güncelleme İşlemi <br>".$subject->title,"");

                header("location: ".SITEADRESS."subject/".$subject->url);

                return $this->render('newsubject',['model'=>$subject,'kategoriler'=>$result,'url'=>$url]);       

            }

            $this->render('editsubject',['model'=>$subject,'kategoriler'=>$result,'url'=>$url]);

        }



        $subjresult = $subject->get($url);

        $subject->title = $subjresult->title;

        $subject->subtitle = $subjresult->subtitle;

        $subject->categoryId = $subjresult->categoriid;

        $subject->content = $subjresult->content;

        return $this->render('editsubject',['model'=>$subject,'kategoriler'=>$result,'url'=>$url]);

    }

}