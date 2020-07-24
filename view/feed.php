<?php
if (!isset($_COOKIE["PHPSESSID"]) || !session_start() || !isset($_SESSION['id']))
    die("Вы не авторизованы!");
require "../database/event_dao.php";
$event_dao = new EventDao();
$data = $event_dao->getFeed();

//ToDo - process remove action

function user_link($event)
{
    return '<a href="/profile?id=' . $event['user_id'] . '">' . $event['name'] . '</a>';
}

function task_link($event)
{
    return '<a href="/tasks?id=' . $event['user_id'] . '">' . $event['description'] . '</a>';
}

function old_value_link($event)
{
    return '<a href="/tasks?id=' . $event['user_id'] . '">' . $event['old_value'] . '</a>';
}

?>
<script>
    function deleteEvent(id) {
        if(!confirm("Вы действительно хотите удалить это событие?"))
            return;
        loadView(document.location.pathname, JSON.stringify({id: id}));
    }

    function addAdminButton(id) {
        const a = document.createElement("span");
        document.currentScript.parentElement.appendChild(a);
        a.classList.add("btn-link", "float-right", "mr-3");
        a.innerText = "Удалить";
        a.addEventListener("click", () => {
            deleteEvent(id);
        });
    }
</script>
<div class="mx-auto text" style="max-width: 800px;">
    <?php foreach ($data as $event):
        echo sprintf('<div class="event">Пользователь %s ', user_link($event));
        switch ($event['type']):
            case 'registered':
                echo 'зарегистрировался на портале';
                break;
            case 'created':
                $aa = $event['is_checklist'] ? 'новый чеклист' : 'новую задачу';
                echo sprintf('создал %s "%s"', $aa, task_link($event));
                break;
            case 'modified':
                $aa = $event['is_checklist'] ? 'чеклиста' : 'задачу';
                echo sprintf('изменил описание %s с "%s" на "%s"', $aa, old_value_link($event), task_link($event));
                break;
            case 'deleted':
                $aa = $event['is_checklist'] ? 'чеклист' : 'задачу';
                echo sprintf('удалил %s "%s"', $aa, task_link($event));
                break;
            case 'done':
                $aa = $event['is_checklist'] ? 'чеклист' : 'задачу';
                echo sprintf('выполнил %s "%s"', $aa, task_link($event));
                break;
            case 'undone':
                $aa = $event['is_checklist'] ? 'чеклиста' : 'задачи';
                echo sprintf('продолжил выполнение  %s "%s"', $aa, task_link($event));
                break;
        endswitch;
        if ($event['comment']) {
            echo ":\n";
            echo '<div class="comment">' . $event['comment'] . '</div>';
        }
        /*echo '<div class="w-100"><script>addAdminButton('.$event['id'].')</script></div>';*/
        echo "</div>\n";
    endforeach; ?>
</div>