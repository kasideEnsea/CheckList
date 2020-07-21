<?php
require('../database/config.php');
require ('../database/dao.php');

if (isset($_POST['login'])) {
    $login = $_POST['login'];
    if ($login == '') {
        unset($login);
    }
}
if (isset($_POST['password'])) {
    $password = $_POST['password'];
    if ($password == '') {
        unset($password);
    }
}
if (isset($_POST['name'])) {
    $name = $_POST['name'];
    if ($name == '') {
        unset($name);
    }
}

if (empty($login) or empty($password) or empty($name))
{
    exit ("Вы ввели не всю информацию, вернитесь назад и заполните все поля!");
}

$login = stripslashes($login);
$login = htmlspecialchars($login);
$password = stripslashes($password);
$password = htmlspecialchars($password);
$name = stripslashes($name);
$name = htmlspecialchars($name);

$login = trim($login);
$password = trim($password);
$name = trim($name);

$conn = Connection::getInstance();

$query = sprintf("SELECT id FROM user WHERE login='$login'");

$myrow = $conn->query($query)->fetch_all(MYSQLI_ASSOC);

if (count($myrow) != 0) {
    exit ("Извините, введённый вами логин уже зарегистрирован. Введите другой логин.");
}
$query = sprintf("INSERT INTO user (login, password, name, deleted) VALUES('$login', '$password', '$name', false)");
$result2 = $conn->query($query);

if ($result2 == 'TRUE') {
    echo "Вы успешно зарегистрированы!";
} else {
    echo "Ошибка! Вы не зарегистрированы.";
}
