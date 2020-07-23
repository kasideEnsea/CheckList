<?php
require ('../database/dao.php');
session_start();
$user = new Object('user');
$user_data = $user->getById($_SESSION['id']);
$name = $user_data['name'];
$img = $user_data['avatar'];
$login = ($_SESSION['login']);
$about = $user_data['about'];
if(!$img){
    $img = "/images/corgi.jpg";
}
if(!$about){
    $about = "Расскажите о себе!";
}
?>

<form class="form-profile" method="post">
    <img src=<? echo ($img) ?> width="10%" align="left" border="0" hspace="2%" vspace="2%" />
    <h4 class="font-weight-normal">
        <?echo("Имя: ".$name)?>
    </h4>
    <h4 class="font-weight-normal"><?echo ("Логин: " . $login)?></h4>
    <h4 class="font-weight-normal">Обо мне:</h4>
    <p><?echo ($about)?></p>

<!--    <h1 class="font-weight-normal">Авторизация</h1>-->
<!--    <label for="inputLogin" class="sr-only">Логин</label>-->
<!--    <input name="login" type="text" class="form-control" id="inputLogin" placeholder="Логин" required autofocus>-->
<!--    <label for="inputPassword" class="sr-only">Пароль</label>-->
<!--    <input name="password" type="password" id="inputPassword" class="form-control" placeholder="Пароль" required>-->
<!--    <button class="btn btn-lg btn-primary btn-block" type="submit">Вход</button>-->
</form>


