<?php
require ('../database/dao.php');
session_start();
$user = new Object('user');
$user_data = $user->getById($_SESSION['id']);
$name = $user_data['name'];
if (!$user_data['avatar']) {
    $img = "/images/corgi.jpg";
} else {
    $img = "/user_images/".$user_data['avatar'];
}

$login = $user_data['login'];
$about = $user_data['about'];
if($_SERVER['REQUEST_METHOD'] == 'POST') {
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
    if (isset($_FILES['userfile']) and $_FILES['userfile']['tmp_name']!="") {
        $uploaddir = $_SERVER['DOCUMENT_ROOT']."/user_images/";
        $filename = $login."_".date("MdYhisA");
        $err = False;
        echo (mime_content_type($_FILES['userfile']['tmp_name']));
        switch (mime_content_type($_FILES['userfile']['tmp_name'])){
            case "image/jpg": {
                $filename .= ".jpg";
                break;
            }
            case "image/jpeg": {
                $filename .= ".jpeg";
                break;
            }
            case "image/png": {
                $filename .= ".png";
                break;
            }
            default:{
                echo ("<script> alert('Неверный тип файла')</script>");
                $err = True;
            }
        }
        if(!$err) {
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
                $img = "/user_images/".$filename;

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
        $_SESSION['login']=$myrow['login'];
    }
}
function h_die($err) {
    echo '<h5 class="text-center">' . $err . '</h5>';
    exit();
}
?>
<script>
    a = document.getElementById("edit_profile");
    a.style.display = "None";
    function hideEditProfile() {
        a.style.display = "";
    }
</script>

<div class="mx-auto text form-profile" style="max-width: 800px;">
    <img src=<?echo ($img)?>
         width="200px" align="left" border="0" hspace="2%" vspace="2%" />
    <h4 class="font-weight-normal">
        <?echo("Имя: ".$name)?>
    </h4>
    <h4 class="font-weight-normal"><?echo ("Логин: " . $login)?></h4>
    <h4 class="font-weight-normal">Обо мне:</h4>
    <p><? if(!$about){
            echo "Расскажите о себе!";
        }else {
            echo $about;
        }?></p>
    <input type="button" class="btn btn-lg btn-primary btn-block" onclick="hideEditProfile()" value="Редактировать">
</div>

<div class="mx-auto text" style="max-width: 600px; border: solid black 1px; margin: 20px">
<form enctype="multipart/form-data" class="mx-auto text form-profile" id="edit_profile" method="post">
    <h5 class="font-weight-normal">Введите новые данные</h5>
    <h5> Логин </h5>
    <input name="login" type="text" id="inputLogin" class="form-control" value="<?echo ($login)?>">
    <h5> Имя </h5>
    <input name="name" type="text" id="inputName" class="form-control" value="<?echo ($name)?>">
    <h5> О себе </h5>
    <textarea name="about" class="form-control" id="inputAbout"><?echo ($about)?></textarea>
    <h5> Аватар </h5>
    <input name="userfile" type="file" />
    <button class="btn btn-lg btn-primary mx-auto" type="submit">Принять</button>
</form>


