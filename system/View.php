<?php

namespace App\System;



use App\System\Application;



class View

{

    public $title = "";

    public $description = "";

    public $scripts = "";

    public $CSS = "";

    public $pluginCss="";

    public $pluginScripts="";



    public $headerString="";

    public function renderView($view,array $params)

    {

        foreach ($params as $key => $value) {

            $$key = $value;

        }

        $layoutName = Application::$app->layout;

        if (Application::$app->controller) {

            $layoutName = Application::$app->controller->layout;

        }

        $className = ucfirst($layoutName);
        require(INCLUDEPATH."/app/controllers/".$className."Controller.php");
        $className = "App\Controllers\\".$className;
        Application::$app->layoutClass = new $className;
        
        
        $viewContent = $this->renderViewOnly($view, $params);
        
        ob_start();

        include_once INCLUDEPATH."/app/views/thema/".THEMANAME."/layouts/$layoutName"."Layout.phhtml";

        $layoutContent = ob_get_clean();

        echo str_replace('{{Content}}', $viewContent, $layoutContent);

    }


    



    public function renderViewOnly($view,$params = [])

    {

        foreach ($params as $key => $value) {

            $$key = $value;

        }

        ob_start();

        include_once INCLUDEPATH."/app/views/thema/".THEMANAME."/$view.phhtml";

        return ob_get_clean();

    }

    public function renderAdminView($view,array $params)

    {

        foreach ($params as $key => $value) {

            $$key = $value;

        }

        $layoutName = Application::$app->layout;

        if (Application::$app->controller) {

            $layoutName = Application::$app->controller->layout;

        }

        $viewContent = $this->renderAdminViewOnly($view, $params);

        ob_start();

        include_once INCLUDEPATH."/app/views/thema/".ADMINTHEMANAME."/layouts/$layoutName"."Layout.phhtml";

        $layoutContent = ob_get_clean();

        echo str_replace('{{Content}}', $viewContent, $layoutContent);

    }

    public function renderAdminViewOnly($view,$params = [])

    {

        foreach ($params as $key => $value) {

            $$key = $value;

        }

        ob_start();

        include_once INCLUDEPATH."/app/views/thema/".ADMINTHEMANAME."/$view.phhtml";

        return ob_get_clean();

    }


    public function renderViewContent($content)

    {

        $layoutName = Application::$app->layout;

        if (Application::$app->controller) {

            $layoutName = Application::$app->controller->layout;

        }

        $className = ucfirst($layoutName);
        require(INCLUDEPATH."/app/controllers/".$className."Controller.php");
        $className = "App\Controllers\\".$className;
        Application::$app->layoutClass = new $className;

        ob_start();

        include_once INCLUDEPATH."/app/views/thema/".THEMANAME."/layouts/$layoutName"."Layout.phhtml";

        $layoutContent = ob_get_clean();
        $content = 
        '
        <section class="m-5 p-5">
            <div class="container">
                <div class="row">
                    <div class col-md-12>
                    '.$content.'
                    </div>
                </div>
            </div>
        </section>
        ';

        echo str_replace('{{Content}}',$content,$layoutContent);

    }

    public function addPartialScripts($scriptFiles = [],$scriptLocal = null)

    {

        $scriptstr = "";

        foreach ($scriptFiles as $script) 

        {

            $scriptstr .= '<script src="scripts/'.$script.'"></script>';   

            // $scriptstr .= '<script src="'.INCLUDEPATH.'/scripts/'.$script.'"></script>';   

        }

        if ($scriptLocal) {



            $scriptstr.="<script type=\"text/javascript\">$scriptLocal</script>";

        }

        $this->scripts = $scriptstr;

    }



    public function addPartialCSS($CSSFiles = [],$CSSLocal = null)

    {

        $CSSstr = "";

        foreach ($CSSFiles as $CSS) 

        {

            // $CSSstr .= '<link rel="stylesheet" src="'.INCLUDEPATH.'/assets/'.$CSS.'"></style>';   

            $CSSstr .= '<link rel="stylesheet" href="assets/'.$CSS.'"></style>';   

        }

        if ($CSSLocal) {



            $CSSstr.="<style>$CSSLocal</style>";

        }

        $this->CSS = $CSSstr;

    }





    public function addPluginScripts($scriptFiles = [],$scriptLocal = null)

    {

        $scriptstr = "";

        foreach ($scriptFiles as $script) 

        {

            $scriptstr .= '<script src="'.$script.'"></script>';   

            // $scriptstr .= '<script src="'.INCLUDEPATH.'/scripts/'.$script.'"></script>';   

        }

        if ($scriptLocal) {



            $scriptstr.="<script type=\"text/javascript\">$scriptLocal</script>";

        }

        $this->pluginScripts = $scriptstr;

    }



    public function addPluginCSS($CSSFiles = [])

    {

        $CSSstr = "";

        foreach ($CSSFiles as $CSS) 

        {

            // $CSSstr .= '<link rel="stylesheet" src="'.INCLUDEPATH.'/assets/'.$CSS.'"></style>';   

            $CSSstr .= '<link rel="stylesheet" href="'.$CSS.'"></style>';   

        }

        $this->pluginCss = $CSSstr;

    }



    public function addheaderString($strings = [])

    {

        $str = "";

        foreach ($strings as $item) 

        {

            $str .= $item;   

        }

        $this->headerString = $str;

    }



}