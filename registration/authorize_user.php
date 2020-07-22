<?php
session_start();
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
$conn = Connection::getInstance();

$login = $conn->escape_string($login);
$login = htmlspecialchars($login);

$login = trim($login);
$password = trim($password);

$result = $conn->query("SELECT * FROM user WHERE login='$login'");
$myrow = mysqli_fetch_array($result);
if (count($myrow) == 0)
{
    exit ("Извините, введённый вами login неверный.");
}
else {
    $password = sha1($password);
    if ($myrow['password']==$password) {
        $_SESSION['login']=$myrow['login'];
        $_SESSION['id']=$myrow['id'];
        $_SESSION['role']=$myrow['role'];
        ?>
        <script>
            localStorage.setItem('login', '<?=$myrow['login']?>')
            localStorage.setItem('role', '<?=$myrow['role']?>')
        </script>
        <?
        echo "Вы успешно вошли на сайт! <a href='login.php'>Главная страница</a>";
    }
    else {
        exit ("Извините, введённый вами пароль неверный.");
    }
}
?>
