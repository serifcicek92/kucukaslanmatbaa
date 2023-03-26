<?php
namespace App\System;

use App\Controllers\Auth;

use App\Models\Kategoriler;

use App\System\Controller;

use App\System\Router;

use App\System\View;



class Application

{

    public static Application $app;

    public Router $router;

    public ?Controller $controller = null;

    public View $view;



    public Kategoriler $kategoriler;

    public Functions $functions;



    public Auth $auth;

    public $classList = [];

    public $layoutClass;




    public string $layout = 'main';

    public function __construct()

    {

        self::$app=$this;

        $this->router = new Router();   

        $this->view =  new View();



        // $this->kategoriler = new Kategoriler();

        $this->functions = new Functions();

        $this->auth = new Auth();

    }





    public function run($url,$callback,$method='get')

    {

        $this->router->resolve($url,$callback,$method);

    }

    public function setController($controller)

    {

        $this->controller = $controller;

    }

    public function getController()

    {

        return $this->controller;

    }

}