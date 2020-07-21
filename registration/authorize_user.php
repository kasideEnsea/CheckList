<?php
session_start();//  вся процедура работает на сессиях. Именно в ней хранятся данные  пользователя, пока он находится на сайте. Очень важно запустить их в  самом начале странички!!!
require('../database/config.php');
require ('../database/сonnection.php');

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

if (empty($login) or empty($password))
{
    exit ("Вы ввели не всю информацию, вернитесь назад и заполните все поля!");
}

$login = stripslashes($login);
$login = htmlspecialchars($login);
$password = stripslashes($password);
$password = htmlspecialchars($password);

$login = trim($login);
$password = trim($password);

$conn = Connection::getInstance();

$result = $conn->query("SELECT * FROM user WHERE login='$login'"); //извлекаем из базы все данные о пользователе с введенным логином
$myrow = mysqli_fetch_array($result);
if (count($myrow) == 0)
{
    exit ("Извините, введённый вами login неверный.");
}
else {
    if ($myrow['password']==$password) {
        //если пароли совпадают, то запускаем пользователю сессию! Можете его поздравить, он вошел!
        $_SESSION['login']=$myrow['login'];
        $_SESSION['id']=$myrow['id'];//эти данные очень часто используются, вот их и будет "носить с собой" вошедший пользователь
        echo "Вы успешно вошли на сайт! <a href='login.php'>Главная страница</a>";
    }
    else {
        exit ("Извините, введённый вами пароль неверный.");
    }
}
?>
