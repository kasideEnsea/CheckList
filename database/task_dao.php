<?php
require_once($_SERVER['DOCUMENT_ROOT']."/database/dao.php");

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

    public function updateByIdAndUserId($set, $id, $user_id)
    {
        $string = self::pack_object($set);
        $this->connection->query('UPDATE ' . $this->table . ' SET ' . $string . ' WHERE id = ' . $id
            . ' AND user_id = ' . $user_id);
        return $this->connection->affected_rows;
    }
}