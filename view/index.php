<div class="text-center">
    <h1>Добро пожаловать на главную страницу</h1>
    <img class="mx-auto" src="/images/corgi.jpg" alt="corgi"/>
    <?php
    if (!array_key_exists("PHPSESSID", $_COOKIE))
        echo
        <<<EOD
    <h1>Вам необходимо авторизоваться для продолжения</h1>
EOD;
    else echo
    <<<EOD
    <h1>Ура, вы авторизованы!!!!</h1>
EOD;
    ?>
</div>