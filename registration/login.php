<?php
    session_start();
    ?>
    <html>
    <head>
    <title>Главная страница</title>
    </head>
    <body>
    <h2>Главная страница</h2>
    <form action="authorize_user.php" method="post">
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

<br>
<a href="registration.php">Зарегистрироваться</a>
    </p></form>
    <br>
    <form action="logout.php" method="post">
    <?php
    if (empty($_SESSION['login']) or empty($_SESSION['id']))
    {
        echo "Вы вошли на сайт, как гость";
    }
    else
    {
        echo "Вы вошли на сайт, как ".$_SESSION['login'];
        ?>
        <p>
            <input type="submit" name="submit" value="Выйти">
        </p>
        <?
    }
    ?>
    </body>
    </html>