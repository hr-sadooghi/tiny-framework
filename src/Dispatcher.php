<?php

use Doctrine\DBAL\DBALException;

class Dispatcher
{
    const CALL_CONTROLLER = 1;
    const RENDER_VIEW     = 2;

    protected $container;

    protected $access_log_id;
    protected $request_start_at;
    protected $target = '';
    protected $request_type;

    protected $request_finish_at;

    public function __construct()
    {
        $this->request_start_at = date('Y-m-d H:i:s');
        //Get service container
        $this->container = Illuminate\Container\Container::getInstance();
    }

    public function dispatchRequest()
    {
        $route = $_GET['r'] ?? false;

        $currentUser = new User();

        //Log request
        $this->access_log_id = $this->logAccessLog();

        $routes = require_once __DIR__ . '/../config/routes.php';
        if (!array_key_exists($route, $routes)) {
            header('Location: http://entekhabat.medu.ir');
            return true;
        }
        if (array_key_exists('r', $routes[$route])) {
            $this->target = '@' . $routes[$route]['r'];
            $this->request_type = self::RENDER_VIEW;
        }


        $middleware = [];
        if (array_key_exists('m', $routes[$route])) {
            $middleware = is_array($routes[$route]['m']) ? $routes[$route]['m'] : [$routes[$route]['m']];
        }

        //middleware auth
        if (in_array('auth', $middleware) && !$currentUser->isLoggedIn()) {
            header('Location: ?r=login');
            return true;
        }

        //middleware csrf
        if (in_array('csrf', $middleware) && !$currentUser->isLoggedIn()) {
            $httpHeaders = getallheaders();
            $validCSRF = $_SESSION['csrf'] ?? null;
            $userCSRF = $httpHeaders['X-CSRF-TOKEN'] ?? $_REQUEST['csrf'] ?? null;
            if ($validCSRF === null || $userCSRF === null || !is_numeric($userCSRF) || $validCSRF != $userCSRF) {
                return $this->sendResponse($this->output(false, '', 'Csrf_Error'));
            }
        }

        //call controller
        if ($this->request_type === self::CALL_CONTROLLER) {
            $controllerClassName = $routes[$route]['c'];
            $action = $routes[$route]['a'];
            $classInstance = new $controllerClassName();

            $classInstance->access_log_id = $this->access_log_id;

//        $this->logAction($controllerClassName, $action, $_POST['p']);

            $this->dispatch($classInstance, $action);
            return true;


        } elseif ($this->request_type === self::RENDER_VIEW) {//call view render
            require_once $routes[$route]['r'];
            return true;
        }
        return true;
    }

    protected function dispatch($classInstance, $action)
    {
        //Define response variable
        $response = [];
        try {
            //Call ActionController
            $response = call_user_func_array(array($classInstance, $action), [$this->container->make('request')]);
            $this->request_finish_at = date('Y-m-d H:i:s');

        } catch (DBALException $DBALException) {//Catch DB Error or Exception
            $response = $this->output(false, '', 'dbError', [$DBALException->getMessage()]);

        } catch (Exception $exception) {//Catch Other Error or Exception
            $response = $this->output(false, '', 'functionFailed', [$exception->getMessage()]);

            //You can Add other exception here such as form validation

        } finally {
            //Prepare and send response
            return $this->sendResponse($response);
        }
    }

    protected function output($success = true, $data = '', $message = '', $debug = [])
    {
        $d = ['data' => $data, 'success' => $success, 'message' => $message];

        //If debug mode enabled return debug information to client side
        if (array_key_exists('debug_tools', $_SESSION)) {
            $d['debug'] = $debug;
        }
        return $d;
    }

    protected function sendResponse($response)
    {
        $out = $response;
        if (is_array($response)) {
            $out = new \Symfony\Component\HttpFoundation\JsonResponse($response);
        }

        if (is_string($response)) {
            $out = new \Symfony\Component\HttpFoundation\Response($response);
        }

        $response_code = $out->getStatusCode();
        $response_body = $out->getContent();

        $this->logResponseLog($response_code, $response_body);

        return $out->send();
    }

    protected function logAccessLog()
    {
        /** @var \Doctrine\DBAL\Connection $logdb */
        $logdb = $this->container->make('log.db');


        $sessionParams = json_encode($_SESSION);

        $postParams = $_POST;
        if (array_key_exists('password', $postParams)) {
            $postParams['password'] = '#secured#';
        }
        $postParams = json_encode($postParams);

        $getParams = json_encode($_GET);

        $user_agent = $_SERVER['HTTP_USER_AGENT'];

        $user_id = array_key_exists('user', $_SESSION) ? $_SESSION['user']['id'] : null;

        $sql = "INSERT INTO access_log(access_date, session_params, post_params, get_params, user_agent, ip, user_id)
                VALUES(?, ?, ?, ?, ?, ?, ?)";
        $params = [$this->request_start_at, $sessionParams, $postParams, $getParams, $user_agent, getUserIP(), $user_id];

        try {
            $logdb->executeUpdate($sql, $params);
        } catch (Exception $e) {
            //in error condition log to file
            file_put_contents('/var/www/election/log/db_error/access_log.log', $sql . "\r\n" . json_encode($params) . "\r\n\r\n", FILE_APPEND);
        }
        return $logdb->lastInsertId();
    }

    protected function logResponseLog($response_code, $response_body)
    {
        /** @var \Doctrine\DBAL\Connection $logdb */
        $logdb = $this->container->make('log.db');

        $sql = "INSERT INTO response_log(access_log_id, target, response_code, response_body, response_datetime)
                VALUES(?, ?, ?, ?, ?)";
        $params = [$this->access_log_id, $this->target, $response_code, $response_body, $this->request_finish_at];

        try {
            $logdb->executeUpdate($sql, $params);
        } catch (Exception $e) {
            //in error condition log to file
            $data = <<<DATA
access_log_id: $this->access_log_id
target: $this->target
request_finish_at: $this->request_finish_at
response_code: $response_code
response_body:
$response_body


DATA;
            file_put_contents('/var/www/election/log/response.log', $data, FILE_APPEND);
        }
    }
}
