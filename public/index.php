<?php
session_start();
//ini_set('display_errors', 1);
require_once __DIR__ . '/../src/bootstrap.php';

$dispatcher = new Dispatcher();

//First try to use primary routes, If route not defined try to call other (ajax_controller) routes.
$r = $dispatcher->dispatchRequest();

if ($r === false) {
    header('Location: index.php?r=login');
}

