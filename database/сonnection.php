<?php
class Connection{
    private static $instance = null;
    private $connection;
    public function __construct() {
        $errorMessage = "You can't connect to database";
        $this->connection = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
        if (!$this->connection){
            throw new Exception($errorMessage);
        }
    }

    public static function getInstance()
    {
        if(!self::$instance)
        {
            self::$instance = new Connection();
        }

        return self::$instance;
    }

    public function query($sql) {
        $data = $this->connection->query($sql);
        if (!$data){
            die(mysqli_error($this->connection));
        }
        return $data;
    }
}
