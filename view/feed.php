<?php
if (!isset($_COOKIE["PHPSESSID"]) || !session_start() || !isset($_SESSION['id']))
    die("Вы не авторизованы!");
require "../database/event_dao.php";
$event_dao = new EventDao();
$data = $event_dao->getFeed();
?>
<div class="mx-auto text" style="max-width: 800px;">
    <?php foreach ($data as $event): ?>
        <pre class="mx-auto text" style="max-width: 600px; background: #eee; margin:20px">
<?=json_encode($event, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)?>
        </pre>
    <? endforeach; ?>
</div>
