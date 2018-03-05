<?php

class Controller
{
    const VERSION = 1;

    /**
     * @var \Doctrine\DBAL\Connection
     */
    public $db;

    /**
     * @var \Doctrine\DBAL\Connection
     */
    public $dbl;

    /**
     * @var Illuminate\Container\Container
     */
    public $container;

    /**
     * @var \Symfony\Component\HttpFoundation\Request
     */
    public $request;

    public $access_log_id;

    public function __construct()
    {
        $_SESSION['duration'] = microtime(true);
        $this->container = Illuminate\Container\Container::getInstance();
        $this->db = $this->container->make('main.dbal');
        $this->dbl = $this->container->make('log.db');
        $this->request = $this->container->make('request');
    }

    const ALL_PARAMS = -1;

    /**
     * @param int  $index
     * @param null $default
     * @return mixed
     */
    public function getParams(int $index = self::ALL_PARAMS, $default = null)
    {
        $params = $this->request->request->get('p');
        if ($index !== self::ALL_PARAMS) {
            $params = $params[$index] ?? $default;
        }
        return $params;
    }

    public function decodeContent($value)
    {
        if (is_string($value)) {
            if (substr($value, 0, 23) === 'data:text/plain;base64,') {
                $value = base64_decode(substr($value, 23));
            }
        }
        return $value;
    }

    public function purifyHtml($input)
    {
        return $this->get('inputCleaner')->cleanXSS($input);
    }

    public function output($success = true, $data = '', $message = '', $debug = [])
    {
        $d = ['data' => $data, 'success' => $success, 'message' => $message];
        //If debug mode enabled return debug information to client side
        if (array_key_exists('debug_tools', $_SESSION)) {
            $d['debug'] = $debug;
        }
        $this->db->close();
        return $d;
    }

    protected function get($serviceName)
    {
        return $this->container->make($serviceName);
    }

    /**
     * return current login user properties
     *
     * @return User
     */
    protected function getUser()
    {
        return $this->get('user');
    }

    function __destruct()
    {
    }
}