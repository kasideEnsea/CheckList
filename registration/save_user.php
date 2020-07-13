<?php
require('Database/config.php');
function connectDB() {
    $errorMessage = 'Невозможно подключиться к серверу базы данных';
    $conn = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
    if (!$conn)
        throw new Exception($errorMessage);
    else {
        $query = $conn->query('set names utf8');
        if (!$query)
            throw new Exception($errorMessage);
        else
            return $conn;
    }
}

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
//!!!хз, нужно ли это
$login = stripslashes($login);
$login = htmlspecialchars($login);
$password = stripslashes($password);
$password = htmlspecialchars($password);

$login = trim($login);
$password = trim($password);

$conn = connectDB();

$query = sprintf("SELECT id FROM users WHERE login='$login'");

$myrow = $conn->query($query)->fetch_all(MYSQLI_ASSOC);

if (!empty($myrow['id'])) {
    exit ("Извините, введённый вами логин уже зарегистрирован. Введите другой логин.");
}
$query = sprintf("ISERT INTO users (login, password) VALUES('$login', '$password')");
$result2 = $conn->query($query);

if ($result2 == 'TRUE') {
    echo "Вы успешно зарегистрированы!";
} else {
    echo "Ошибка! Вы не зарегистрированы.";
}
