<?php
require($_SERVER['DOCUMENT_ROOT']."/database/dao.php");

class TaskDao extends Object
{
    public function __construct()
    {
        parent::__construct('task');
    }

    public function getAllByUserIdNotDeleted($user_id)
    {
        $query = sprintf("select * from `$this->table` where user_id = $user_id and not deleted");
        return $this->connection->query($query)->fetch_all(MYSQLI_ASSOC);
    }

    public function getAllByUserIdAndParentId($user_id, $parent = 0)
    {
        $ps = $parent ? " = " . $parent : "IS NULL";
        $query = sprintf('select * from `%s` where user_id = `%d` and parent %d', $this->table, $user_id, $ps);
        return $this->connection->query($query)->fetch_all(MYSQLI_ASSOC);
    }

    public function getByIdAndUserId($id, $user_id)
    {
        return $this->connection->query('SELECT * FROM ' . $this->table . ' WHERE id = ' . $id
            . ' AND user_id = ' . $user_id)->fetch_object();
    }

    public function updateByIdAndUserId($set, $id, $user_id)
    {
        $string = '';
        foreach ($set as $key => $value) {
            if ($string != '') {
                $string .= ',';
            }
            $string .= " " . $key . "='" . $value . "'";
        }
        $this->connection->query('UPDATE ' . $this->table . ' SET ' . $string . ' WHERE id = ' . $id
            . ' AND user_id = ' . $user_id);
    }
}