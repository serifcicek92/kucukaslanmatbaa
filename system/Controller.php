<?php

namespace App\System;



use App\System\Application;



class Controller

{

    public string $layout ='main';

    public function setLayout($layout)

    {

        $this->layout = $layout;

    }

    public function render($view,array $params)

    {

       Application::$app->view->renderView($view,$params);

    }

    public function renderAdmin($view,array $params)

    {

       Application::$app->view->renderAdminView($view,$params);

    }




    public function renderViewOnly($view,$params = [])

    {

        return Application::$app->view->renderViewOnly($view,$params);

    }

    public function renderViewContent($content)

    {

         Application::$app->view->renderViewContent($content);

    }



}