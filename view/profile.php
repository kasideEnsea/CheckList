<?php
if (!isset($_COOKIE["PHPSESSID"]) || !session_start() || !isset($_SESSION['id']))
    die("Вы не авторизованы!");
$my_user_id = $_SESSION['id'];
$req_user_id = isset($_GET['id']) ? $_GET['id'] : $my_user_id;

require('../database/dao.php');
session_start();
$user = new Object('user');
$user_data = $user->getById($req_user_id);
if (!$user_data)
    die('<h2 class="text-center">Пользователь не найден</h2>');
$name = $user_data['name'];
$img = $user_data['avatar'] ? "/user_images/" . $user_data['avatar'] : "/images/corgi.jpg";
$login = $user_data['login'];
$about = $user_data['about'];
if ($my_user_id == $req_user_id && $_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['login'])) {
        $newlogin = $_POST['login'];
        if ($login == '') {
            unset($login);
        }
    }
    if (isset($_POST['about'])) {
        $newabout = $_POST['about'];
        if ($newabout == '') {
            unset($newabout);
        }
    }
    if (isset($_POST['name'])) {
        $newname = $_POST['name'];
        if ($newname == '') {
            unset($newname);
        }
    }
    if (isset($_FILES['userfile']) and $_FILES['userfile']['tmp_name'] != "") {
        $uploaddir = $_SERVER['DOCUMENT_ROOT'] . "/user_images/";
        $filename = $login . "_" . date("MdYhisA");
        $err = False;
        echo(mime_content_type($_FILES['userfile']['tmp_name']));
        switch (mime_content_type($_FILES['userfile']['tmp_name'])) {
            case "image/jpg":
            {
                $filename .= ".jpg";
                break;
            }
            case "image/jpeg":
            {
                $filename .= ".jpeg";
                break;
            }
            case "image/png":
            {
                $filename .= ".png";
                break;
            }
            default:
            {
                echo("<script> alert('Неверный тип файла')</script>");
                $err = True;
            }
        }
        if ($_FILES['userfile']['size'] > 5 * 1024 * 1024) {
            echo("<script> alert('Размер файла превышает 5 МБ')</script>");
            $err = True;
        }
        if (!$err) {
            $uploadfile = $uploaddir . $filename;
            if (!file_exists($uploaddir)) {
                mkdir($uploaddir, 0700);
            }
            if (move_uploaded_file($_FILES['userfile']['tmp_name'], $uploadfile)) {
                echo("<script> alert('Файл корректен и был успешно загружен')</script>");
                $array = array(
                    "avatar" => $filename,
                );
                $user->updateById($array, $_SESSION['id']);
                $img = "/user_images/" . $filename;

            } else {
                echo("<script> alert('Возможная атака с помощью файловой загрузки!')</script>");
            }
        }
    }

    $conn = Connection::getInstance();

    $newlogin = $conn->escape_string($newlogin);
    $newlogin = htmlspecialchars($newlogin);
    $newname = $conn->escape_string($newname);
    $newname = htmlspecialchars($newname);
    $newabout = htmlspecialchars($newabout);

    $newlogin = trim($newlogin);
    $newabout = trim($newabout);
    $newname = trim($newname);

    if ($about != $newabout) {
        $array = array(
            "about" => $newabout,
        );
        $user->updateById($array, $_SESSION['id']);
        $about = $newabout;

    }
    if ($name != $newname) {
        $array = array(
            "name" => $newname,
        );
        $user->updateById($array, $_SESSION['id']);
        $name = $newname;
    }

    if ($login != $newlogin) {

        $query = sprintf("SELECT id FROM user WHERE login='$newlogin'");

        $myrow = $conn->query($query)->fetch_all(MYSQLI_ASSOC);

        if (count($myrow) != 0) {
            h_die("Извините, введённый вами логин уже зарегистрирован. Введите другой логин.");
        }
        $array = array(
            "login" => $newlogin,
        );
        $user->updateById($array, $_SESSION['id']);
        $login = $newlogin;
        $_SESSION['login'] = $myrow['login'];
    }
}
function h_die($err)
{
    echo '<h5 class="text-center">' . $err . '</h5>';
    exit();
}

?>
<script>
    function hideEditProfile() {
        const a = document.getElementById("edit_profile");
        a.style.display = a.style.display ? "" : "none";
    }
</script>
<div class="mx-auto text form-profile" style="max-width: 800px;">
    <table id="profileTable">
        <tr>
            <td>
                <img src="<?= $img ?>" width="200px" alt="avatar"/>
            </td>
            <td id="main-cell">
                <h4 class="font-weight-normal">
                    <? echo("Имя: " . $name) ?>
                </h4>
                <h4 class="font-weight-normal"><? echo("Логин: " . $login) ?></h4>
                <? if ($about || $req_user_id == $my_user_id): ?>
                    <h4 class="font-weight-normal">О себе:</h4>
                    <p><?= $about ? $about : "Расскажите о себе!" ?></p>
                <? endif; ?>
                <? if ($req_user_id == $my_user_id): ?>
                    <input type="button" class="btn btn-lg btn-primary" onclick="hideEditProfile()"
                           value="Редактировать">
                <? endif; ?>
            </td>
        </tr>
    </table>

    <? if ($req_user_id == $my_user_id): ?>
        <div class="mx-auto text" id="edit_profile" style="display: none;">
            <form enctype="multipart/form-data" class="mx-auto text form-profile" method="post">
                <h5 class="font-weight-normal">Введите новые данные</h5>
                <label for="inputLogin" class="h5">Логин</label>
                <input name="login" type="text" id="inputLogin" class="form-control" value="<? echo($login) ?>">
                <label for="inputName" class="h5">Имя</label>
                <input name="name" type="text" id="inputName" class="form-control" value="<? echo($name) ?>">
                <label for="inputAbout" class="h5">О себе</label>
                <textarea name="about" class="form-control" id="inputAbout"><? echo($about) ?></textarea>
                <div class="mt-3">
                    <label class="h5" for="userfileInput">Аватар</label>
                    <input name="userfile" id="userfileInput" type="file"/>
                    <button class="btn btn-lg btn-primary mx-auto" type="submit">Принять</button>
                </div>
            </form>
        </div>
    <? endif; ?>

    <?php
    require("../database/event_dao.php");
    session_start();
    $object = new EventDao();
    $id = $_SESSION['id'];
    $data = $object->getLastDone($id);
    $arr = [];
    foreach ($data as $value) {
        $arr[$value['date']] = $value['count'];
    }
    $today = date_create(date("Y-M-d"));
    $lastDay = date_modify($today, '-6 day');
    $dataString = "<tr>";
    $colorString = "<tr>";
    $max = max($arr);
    for ($i = 0; $i < 7; $i++) {
        $dataString .= "<td>" . date_format($lastDay, 'Y-m-d') . "</td>";
        if (!array_key_exists(date_format($lastDay, 'Y-m-d'), $arr)) {
            $colorString .= "<td style=\"background-color: rgb(255, 255, 255);\" height = 40px></td>";
        } else {
            $value = $arr[date_format($lastDay, 'Y-m-d')];
            $colorString .= "<td style=\"background-color: rgba(0, 0, 255, " . getColor($value, $max) . ");\" height = 40px>$a</td>";
        }
        $lastDay = date_modify($today, '+1 day');
    }
    $dataString .= "</tr>";
    $dataString .= "</tr>";

    function getColor($value, $max)
    {
        return $value / $max;
    }

    ?>
    <div class="mx-auto text" style="max-width: 600px; margin: 20px">
        <table border="1">
            <caption>Мои успехи за последние 7 дней</caption>
            <? echo $dataString;
            echo $colorString ?>
        </table>

        <a href="/tasks?id=<?= $req_user_id ?>" class="d-block text-center">
            <?= $req_user_id == $my_user_id ? "Просмотреть свои задачи" : "Просмотреть задачи пользователя" ?>
        </a>
    </div>
</div>

