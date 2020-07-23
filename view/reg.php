<form method="post">
    <p>
        <label>Ваше имя:<br></label>
        <input name="name" type="text" size="50" maxlength="50">
    </p>
    <p>
        <label>Ваш логин:<br></label>
        <input name="login" type="text" size="15" maxlength="15">
    </p>
    <p>
        <label>Ваш пароль:<br></label>
        <input name="password" type="password" size="15" maxlength="15">
    </p>
    <p>
        <input type="submit" name="submit" value="Зарегистрироваться">
    </p>
</form>

<?php
if($_SERVER['REQUEST_METHOD'] == 'POST') {
    require($_SERVER['DOCUMENT_ROOT'] . '/database/сonnection.php');

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

    if (empty($login) or empty($password) or empty($name)) {
        exit ("Вы ввели не всю информацию, вернитесь назад и заполните все поля!");
    }

    $conn = Connection::getInstance();

    $login = $conn->escape_string($login);
    $login = htmlspecialchars($login);
    $name = $conn->escape_string($name);
    $name = htmlspecialchars($name);

    $login = trim($login);
    $password = trim($password);
    $name = trim($name);

    $query = sprintf("SELECT id FROM user WHERE login='$login'");

    $myrow = $conn->query($query)->fetch_all(MYSQLI_ASSOC);

    if (count($myrow) != 0) {
        exit ("Извините, введённый вами логин уже зарегистрирован. Введите другой логин.");
    }
    $password = sha1($password);
    $query = sprintf("INSERT INTO user (login, password, name, deleted) VALUES('$login', '$password', '$name', false)");
    $result2 = $conn->query($query);

    if ($result2 == 'TRUE') {
        echo "Вы успешно зарегистрированы!";
    } else {
        echo "Ошибка! Вы не зарегистрированы.";
    }
}
?>
