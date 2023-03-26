<?php

//[SCRIPT_NAME] => /scFrameWork/index.php
$dirname = dirname($_SERVER["SCRIPT_NAME"]);//=>/scFrameWork
$basename = basename($_SERVER["SCRIPT_NAME"]);//=>index.php


//#region local
$request_uri = $_SERVER["REQUEST_URI"];//[REQUEST_URI] => /scFrameWork/
$baseHref = $_SERVER["HTTP_HOST"].$dirname;
// $baseHref = $_SERVER["HTTP_HOST"].$dirname."/";
//#endregion


//#region web site kısmı
//$request_uri = str_replace([$dirname,$basename],null,$_SERVER["REQUEST_URI"]);//=> /
//$baseHref = $_SERVER["HTTP_HOST"].$dirname;
//#endregion
// echo '<base href="http://'.$baseHref.'"/>';

define('BASEURL', '<base href="http://'.$baseHref.'"/>');

