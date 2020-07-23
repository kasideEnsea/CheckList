<?php
require($_SERVER['DOCUMENT_ROOT']."/database/сonnection.php");
class Object {
    protected $table;
    protected $connection;

    public function __construct($table) {
        $this->setTable($table);
        $this->connection = Connection::getInstance();
    }

    public function getTable() { return $this->table; }

    public function setTable($name) { $this->table = $name; }
    public function getConnection() { return $this->connection; }

    public function getAll() {
        $query = sprintf('select * from `%s`', $this->table);
        return $this->connection->query($query)->fetch_all(MYSQLI_ASSOC);
    }

    public function getById($id) {
        return $this->connection->query('SELECT * FROM ' . $this->table . ' WHERE id = ' . $id)->fetch_object();
    }

    public function insert($data) {
        $callback = function ($value) {
            if($value === false)
                return 0;
            if($value)
                return '\''.$this->connection->escape_string($value).'\'';
            return "NULL";
        };
        $keys = join(", ", array_keys($data));
        $values = array_map($callback, array_values($data));
        $values = join(", ", $values);
        $sql = "INSERT INTO `$this->table` ($keys) VALUES ($values)";
        $this->connection->query($sql);
    }

    public function updateById($set, $id){
        $string = self::pack_object($set);
        $this->connection->query('UPDATE ' . $this->table . ' SET ' . $string . ' WHERE id = ' . $id);
    }

    public function deleteById($id) {
        $this->connection->query('DELETE FROM ' . $this->table . ' WHERE id = ' . $id);
    }

    public static function pack_object($set)
    {
        $string = '';
        foreach ($set as $key => $value) {
            if ($string != '') {
                $string .= ',';
            }
            if($value === false)
                $value = 0;
            $string .= " " . $key . "='" . $value . "'";
        }
        return $string;
    }
}