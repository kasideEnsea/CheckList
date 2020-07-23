<?php
if (!isset($_COOKIE["PHPSESSID"]) || !session_start() || !isset($_SESSION['id']))
    die("Вы не авторизованы!");?>
<style>
    #task div > span {
        cursor: pointer;
    }

    #task-container input {
        margin: 5px;
    }

    #task-container input[type="text"] {
        width: 300px;
    }
</style>
<div class="mx-auto text" style="max-width: 800px;" id="task-container">
    <h1>Мои задачи</h1>
    <div id="task"></div>
    <br/>
    <h5>Добавить новый чеклист:</h5>
    <div id="task-form"></div>
</div>
<script src="/view/tasks.js"></script>