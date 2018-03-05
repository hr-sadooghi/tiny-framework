<?php
//set environment
$dev = array_key_exists('HTTP_HOST', $_SERVER) && ($_SERVER['HTTP_HOST'] === 'localhost' || strpos($_SERVER['HTTP_HOST'], 'localhost') === 0);
putenv('ENV=' . ($dev ? 'DEV' : 'PROD'));

//main db config
putenv('DB_HOST=' . (getenv('ENV') === 'PROD' ? 'localhost' : 'localhost'));
putenv('DB_USER=' . (getenv('ENV') === 'PROD' ? 'root' : 'root'));
putenv('DB_PASS=' . (getenv('ENV') === 'PROD' ? '123' : '123'));
putenv('DB_NAME=dbname');
putenv('LOG_QUERY_COUNT=false');

//log db config
putenv('LOG_DB_STATUS=' . ($dev ? '0' : '1'));
putenv('LOG_DB_HOST=' . (getenv('ENV') === 'PROD' ? 'localhost' : 'localhost'));
putenv('LOG_DB_USER=' . (getenv('ENV') === 'PROD' ? 'root' : 'root'));
putenv('LOG_DB_PASS=' . (getenv('ENV') === 'PROD' ? '123' : '123'));
putenv('LOG_DB_NAME=' . (getenv('ENV') === 'PROD' ? 'log_dbname' : 'log_dbname'));

putenv('LOG_FILE_NAME=log.log');
putenv('LOG_ERROR_TYPES=' . (E_ERROR | E_WARNING | E_NOTICE));
putenv('ERROR_LOG=' . __DIR__ . '/../log/php-error/' . date('Y-m-d') . '.log');
putenv('TIMEZONE=Asia/Tehran');
