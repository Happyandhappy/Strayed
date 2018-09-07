<?php
/*
if(empty($_SERVER['HTTPS']) || $_SERVER['HTTPS'] == "off"){
    if (substr($_SERVER['HTTP_HOST'], 0, 4) !== 'www.')$zaw="www.";else $zaw="";
    $redirect = 'https://' . $zaw . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
    header('HTTP/1.1 301 Moved Permanently');
    header('Location: ' . $redirect);
    exit();
}
if (substr($_SERVER['HTTP_HOST'], 0, 4) !== 'www.'){
    $zaw="www.";
    $redirect = 'https://' . $zaw . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
    header('HTTP/1.1 301 Moved Permanently');
    header('Location: ' . $redirect);
    exit();
}
*/


ini_set("zlib.output_compression", "On");
include_once ("config.php");
include_once ("global.php");
include_once ("crudPDO.php");
include_once ("users.php");
include_once ("defmodel.php");
include_once ("view.php");
include_once ("controller.php");
?>