<?php

class Logger implements Doctrine\DBAL\Logging\SQLLogger
{
    /**
     * @var \Doctrine\DBAL\Connection
     */
    protected $logDb;

    protected $startTime;
    protected $stopTime;
    protected $executeDateTime;

    protected $sql;
    protected $params;
    protected $types;

    protected $log_data;

    public function __construct($dbal = null)
    {
        $this->logDb = $dbal;
    }

    /**
     * Logs a SQL statement somewhere.
     *
     * @param string     $sql    The SQL to be executed.
     * @param array|null $params The SQL parameters.
     * @param array|null $types  The SQL parameter types.
     *
     * @return void
     */
    public function startQuery($sql, array $params = null, array $types = null)
    {
        $this->sql = $sql;
        $this->params = $params;
        $this->types = $types;

        $this->log_data = array(
            'datetime' => date('Y-m-d H:i:s'),
            'ip'       => getUserIP(),
            'types'    => json_encode($types, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE),
            'sql'      => $sql,
            'params'   => json_encode($this->params, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE),
            'user_id'  => $_SESSION['user_id'] ?? null
        );

        $this->startTime = microtime(true);
    }

    /**
     * Marks the last started query as stopped. This can be used for timing of queries.
     *
     * @return void
     */
    public function stopQuery()
    {
        $this->stopTime = microtime(true);
        if ($this->log_data) {
            $sql = "INSERT INTO `log_query_myisam`(id, query_datetime, ip, types, `sql`, params, user_id)
                    VALUES (NULL, ?, ?, ?, ?, ?, ?)";

            $params = [
                $this->log_data['datetime'],
                $this->log_data['ip'],
                $this->log_data['types'],
                $this->log_data['sql'],
                $this->log_data['params'],
                $this->log_data['user_id']
            ];
            try {
                $this->logDb->executeUpdate($sql, $params);
            } catch (Exception $e) {
                file_put_contents("/var/www/election/log/log.db.error.log", "err" . $this->log_data['username'] . $e->getMessage(), FILE_APPEND);
            }
        }
    }
}
