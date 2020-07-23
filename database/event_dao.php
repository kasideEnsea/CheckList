<?php
require_once($_SERVER['DOCUMENT_ROOT'] . "/database/dao.php");
define("MAX_COUNT", 100);

class EventDao extends Object
{
    public function __construct()
    {
        parent::__construct('event');
    }

    public function getFeed()
    {
        $sql = "SELECT ev.id, ev.user_id, ev.task_id, ev.comment, ev.type, u.name, t.description, ev.old_value, ev.created
FROM `event` ev
LEFT JOIN `user` u ON ev.user_id = u.id
LEFT JOIN `task` t ON ev.task_id = t.id
ORDER BY ev.id DESC
LIMIT " . MAX_COUNT;
        return $this->connection->query($sql)->fetch_all(MYSQLI_ASSOC);
    }
}
