<?php
use Stylite\App;

define('ROOT', realpath('../'));
date_default_timezone_set("PRC");

require "../vendor/autoload.php";

$app = App::getInstance();
$app->start();

require "../app/routes.php";



