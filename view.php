<?php
$path = "view/";
$main = "main.html";
$default = "default.html";
$page = trim($_SERVER['QUERY_STRING'], "/");
$file = $path.$default;
if(empty($page))
    $file = $path.$main;
else if(file_exists($path.$page.".php"))
    $file = $path.$page.".php";
else if(file_exists($path.$page.".html"))
    $file = $path.$page.".html";
include $file;