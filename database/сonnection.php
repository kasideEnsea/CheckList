<?php
$cf = $_SERVER['DOCUMENT_ROOT']."/database/config.php";
if (file_exists($cf)) {
    require_once($cf);
} else {
    die("Для корректной работы нужно создать конфигурационный файл config.php со следующими константами:</br>
DB_HOST, DB_USER, DB_PASSWORD, DB_NAME, DB_TABLE_VERSIONS");
}

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

    public function escape_string($value)
    {
        return $this->connection->escape_string($value);
    }
}
