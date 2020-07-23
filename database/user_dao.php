<?php
require_once($_SERVER['DOCUMENT_ROOT']."/database/dao.php");

class UserDao extends Object
{
    public function __construct()
    {
        parent::__construct('user');
    }
}
