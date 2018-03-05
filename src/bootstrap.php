<?php

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/functions.php';

//Find and check HTTP Request Headers to fine and check 'X-Requested-With' value by 'HamRequest'.
$httpHeaders   = getallheaders();
$requestedWith = $httpHeaders['X-PARAMS-FORMAT'] ?? null;

//Request is one of this situation:
if ($requestedWith === 'JSON-STRINGIFY') {

    //`p` should be exist.
    $_POST['p'] = $_POST['p'] ?? $_GET['p'] ?? array();

    if (is_string($_POST['p'])) {

        //If has request params has specific prefix, remove it.
        if (substr($_POST['p'], 0, 22) === 'data:application/json,') {
            $_POST['p'] = substr($_POST['p'], 22);
        }

        $jsonDecode = json_decode($_POST['p'], true);
        if ($jsonDecode !== null) {
            $_POST['p'] = $jsonDecode;
        }
    }
}

//Initiate services, one of services is request object
require_once __DIR__ . '/ServiceProvider.php';

date_default_timezone_set(getenv('TIMEZONE'));

if (getenv('ENV') === 'PROD' && getenv('LOG_DB_STATUS')) {
    Log::turnOnQueryLog();
} else {
    Log::turnOffQueryLog();
}

//in production environment turn on error log
if (getenv('ENV') === 'PROD') {
    Log::registerErrorHandler(getenv('LOG_ERROR_TYPES'));
}

