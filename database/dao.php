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

    public function updateById($set, $id){
        $string = '';
        foreach ($set as $key => $value) {
            if ($string != ''){
                $string .= ',';
            }
            $string .= " " . $key . "='" . $value . "'";
        }
        echo ('UPDATE ' . $this->table . ' SET ' . $string . ' WHERE id = ' . $id);
        $this->connection->query('UPDATE ' . $this->table . ' SET ' . $string . ' WHERE id = ' . $id);
    }

    public function deleteById($id) {
        $this->connection->query('DELETE FROM ' . $this->table . ' WHERE id = ' . $id);
    }
}