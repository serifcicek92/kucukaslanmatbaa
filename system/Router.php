<?php
namespace App\System;

use ReflectionClass;
use App\System\Application;
use stdClass;

class Router
{

    public function parseUrl()

    {

        $dirname = dirname($_SERVER["SCRIPT_NAME"]);

        $dirname = ($dirname == "/" ? "" : $dirname);

        $basename = basename($_SERVER["SCRIPT_NAME"]);

        $requestUri = str_replace([$dirname, $basename], "", $_SERVER["REQUEST_URI"]);

        $requestUri = (str_starts_with($requestUri, '/') ? $requestUri : "/" . $requestUri);

        return $requestUri;

    }

    public function resolve($url, $callback, $method = 'get')

    {

        $method = explode('/', strtoupper($method));

        

        if (in_array($_SERVER['REQUEST_METHOD'], $method)) {

            $patterns = [

                '{url}' => '([0-9a-zA-Z-]+)',

                '{id}' => '([0-9]+)',

                '{val}' => '([0-9a-zA-Z-]+)'

            ];



            $url = str_replace(array_keys($patterns), array_values($patterns), $url);

           

            $requestUri = self::parseUrl();

            $queriesExpld = explode('?',$requestUri);

            $requestUri = $queriesExpld[0];

            

            if (preg_match('@^' . $url . '$@', $requestUri, $parameters)) {

                unset($parameters[0]);

                if (isset($queriesExpld) && count($queriesExpld)>1) {

                    array_push($parameters,["query"=>$queriesExpld[1]]);

                }

                if (is_callable($callback)) {

                    call_user_func_array($callback, $parameters);

                }

                $controller = explode('/', $callback);

                $className = explode('/', $controller[0]);

                $className = ucfirst(end($className));



                $controllerFile = INCLUDEPATH . '/app/controllers/' . ucfirst($controller[0]) . 'Controller.php';




                if (file_exists($controllerFile)) {

                    $namespace = "App\Controllers\\" . $className;

                    if(!class_exists($namespace)){
                        // if (file_exists(INCLUDEPATH . '/app/controllers/PartialController.php')) {
                        //     require(INCLUDEPATH . '/app/controllers/PartialController.php');
                        // }

                        require $controllerFile;

                    }

                    

                    $class = new $namespace;

                    array_push(Application::$app->classList, $class);

                    call_user_func_array([new $class, $controller[1]], $parameters);

                }

            }

        }

    }

}