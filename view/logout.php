<?php
session_start();
$_SESSION['login'] = '';
$_SESSION['id'] = '';
session_start();
setcookie(session_name(), "", -1, "/");
session_destroy();
session_write_close();
?>
<script>
    localStorage.removeItem('user_id')
    localStorage.removeItem('role')
</script>
<h2 class="text-center">Вы вышли из системы</h2>
