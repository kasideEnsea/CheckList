<?php
switch ($_SERVER["QUERY_STRING"]) {
    case "set":
        session_start();
        echo '<h2 class="text-center">Вы авторизовались</h2>';
        break;

    case "unset":
        session_start();
        setcookie (session_name(), "", -1, "/");
        session_destroy();
        session_write_close();
        echo '<h2 class="text-center">Вы вышли из системы</h2>';
        break;
}?>
<script>
    setTimeout(() => loadView("/"), 1000);
</script>
