<?php
if(!isset($_COOKIE["PHPSESSID"]))
    die("Вы не авторизованы!");
session_start();
if (isset($_REQUEST["name"])) {
    $_SESSION["name"] = htmlentities($_REQUEST["name"]);
}

if (!isset($_SESSION["name"]) || strlen($_SESSION["name"]) == 0) {
    echo "<h1>Я не знаю, как вас зовут :-(</h1>";
} else {
    echo "<h1>Вас зовут ".$_SESSION["name"]."!</h1>";
}?>

<form method="post">
    <label>
        Ваше имя:
        <input name="name" type="text" size="15" maxlength="15">
    </label>
    <input type="submit" value="Отправить"/>
</form>
