<?php

use Doctrine\DBAL\DriverManager;
use Doctrine\DBAL\Configuration;

$container = Illuminate\Container\Container::getInstance();

//Define all service providers here

$container->instance('request', Symfony\Component\HttpFoundation\Request::createFromGlobals());

$container->singleton('user', function ($app) {
    return new User();
});


$container->singleton('log.db', function ($app) {
    $config           = new Configuration();
    $connectionParams = array(
        'dbname'   => getenv('LOG_DB_NAME'),
        'user'     => getenv('LOG_DB_USER'),
        'password' => getenv('LOG_DB_PASS'),
        'host'     => getenv('LOG_DB_HOST'),
        'driver'   => 'mysqli',
        'charset'  => 'UTF8'
    );
    $dbal             = DriverManager::getConnection($connectionParams, $config);
    return $dbal;
});

$container->instance('logger', function ($app) {
    return new Logger($app->make('log.db'));
});

$container->singleton('main.dbal', function ($app) {
    $config           = new Configuration();
    $connectionParams = array(
        'dbname'   => getenv('DB_NAME'),
        'user'     => getenv('DB_USER'),
        'password' => getenv('DB_PASS'),
        'host'     => getenv('DB_HOST'),
        'driver'   => 'mysqli',
        'charset'  => 'UTF8'
    );

    $db = DriverManager::getConnection($connectionParams, $config);
    $db->getConfiguration()->setSQLLogger(new Logger($app->make('log.db')));
    return $db;
});
