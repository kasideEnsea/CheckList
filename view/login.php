<?php
switch ($_SERVER["QUERY_STRING"]) {
    case "set":
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
                    loadView("/");
                </script>
                <?
            }
            else {
                exit ("Извините, введённый вами пароль неверный.");
            }
        }
        }
        ?>
        <form method="post">
            <p>
                <label>Ваш логин:<br></label>
                <input name="login" type="text" size="15" maxlength="15">
            </p>

            <p>
                <label>Ваш пароль:<br></label>
                <input name="password" type="password" size="15" maxlength="15">
            </p>

            <p>
                <input type="submit" name="submit" value="Войти">
            </p>
            <br>
        </form>
        <?php
        break;

    case "unset":
        session_start();
        $_SESSION['login'] = '';
        $_SESSION['id'] = '';
        session_start();
        setcookie(session_name(), "", -1, "/");
        session_destroy();
        session_write_close();
        ?>
        <script>
            localStorage.setItem('login', "")
            localStorage.setItem('role', "")
        </script>
        <?
        echo '<h2 class="text-center">Вы вышли из системы</h2>';
        break;
}?>
