<?php
require ('../database/сonnection.php');
session_start();
if($_SERVER['REQUEST_METHOD'] == 'POST'){
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

        $conn = Connection::getInstance();

        $login = $conn->escape_string($login);
        $login = htmlspecialchars($login);

        $login = trim($login);
        $password = trim($password);

        $result = $conn->query("SELECT * FROM user WHERE login='$login'");
        $myrow = mysqli_fetch_array($result);
        if (count($myrow) == 0)
        {
            $err = "Извините, введённый вами логин неверный.";
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
                    loadView("/");
                </script>
                <?
                exit();
            }
            else {
                $err = "Извините, введённый вами пароль неверный.";
            }
        }
}
?>

<form class="form-signin" method="post">
        <h1 class="font-weight-normal">Авторизация</h1>
        <label for="inputLogin" class="sr-only">Логин</label>
        <input name="login" type="text" class="form-control" id="inputLogin" placeholder="Логин" required autofocus>
        <label for="inputPassword" class="sr-only">Пароль</label>
        <input name="password" type="password" id="inputPassword" class="form-control" placeholder="Пароль" required>
        <button class="btn btn-lg btn-primary btn-block" type="submit">Вход</button>
</form>
<?php if(isset($err))
    echo '<h5 class="text-center">'.$err.'</h5>';
?>


