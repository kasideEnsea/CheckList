<form class="form-signin">
    <form method="post">
        <h1 class="font-weight-normal">Регистрация</h1>
        <label for="inputName" class="sr-only">Имя</label>
        <input name="name" type="text" class="form-control" id="inputName" placeholder="Имя" required autofocus>
        <label for="inputLogin" class="sr-only">Логин</label>
        <input name="login" type="text" class="form-control" id="inputLogin" placeholder="Логин" required>
        <label for="inputPassword" class="sr-only">Пароль</label>
        <input name="password" type="password" id="inputPassword" class="form-control" placeholder="Пароль" required>
        <button class="btn btn-lg btn-primary btn-block" type="submit">Зарегистрироваться</button>
    </form>

    <?php
if($_SERVER['REQUEST_METHOD'] == 'POST') {
    require($_SERVER['DOCUMENT_ROOT'] . '/database/сonnection.php');
    require($_SERVER['DOCUMENT_ROOT'] . '/database/event_dao.php');

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
        h_die ("Извините, введённый вами логин уже зарегистрирован. Введите другой логин.");
    }
    $password = sha1($password);
    $query = sprintf("INSERT INTO user (login, password, name, deleted) VALUES('$login', '$password', '$name', false)");
    $result2 = $conn->query($query);

    if ($result2 == 'TRUE') {
        if($conn->insert_id == 1) {
            $query = sprintf("UPDATE user SET role = 'admin' WHERE id=1");
        }
            $eventDao = new EventDao();
            $obj = array(
                "user_id" => $conn->insert_id,
                "type" => "registered",
            );
            $eventDao->insert($obj);
        h_die("Вы успешно зарегистрированы!");
    } else {
        h_die("Ошибка! Вы не зарегистрированы.");
    }
}

function h_die($err) {
        echo '<h5 class="text-center">' . $err . '</h5>';
        exit();
}
?>
