<?php
session_start();
$_SESSION['login']='';
$_SESSION['id']='';
session_start();
setcookie (session_name(), "", -1, "/");
session_destroy();
session_write_close();
?>
    <script>
        localStorage.setItem('login', null)
        localStorage.setItem('role', null)
    </script>
<?
echo ("Вы вышли");
