<?php
if (!isset($_COOKIE["PHPSESSID"]) || !session_start() || !isset($_SESSION['id']))
    die("Вы не авторизованы!");
header('Content-Type: text/plain');
$my_user_id = $_SESSION['id'];
$req_user_id = isset($_GET['id']) ? $_GET['id'] : $my_user_id;

require("../database/task_dao.php");
require("../database/user_dao.php");
require("../database/event_dao.php");
$task_dao = new TaskDao();
$user_dao = new UserDao();
$event_dao = new EventDao();
$user_id = $user_dao->getById($req_user_id)['id'];
$username = $user_dao->getById($req_user_id)['name'];

$myData = json_decode(file_get_contents('php://input'), true);
if($req_user_id != $my_user_id) {
    print_data_object();
    exit();
}
switch ($_SERVER['REQUEST_METHOD']) {
    case 'GET':
        //Do nothing
        break;
    case 'POST':
        $obj = array(
            'user_id' => $my_user_id,
            'description' => $myData['description'],
            'parent_id' => $myData['parent_id']
        );
        $id = $task_dao->insert($obj);
        log_event($id, "created", $myData['comment'], null);
        break;

    case 'PUT':
        $obj = [];
        if (isset($myData['description'])) {
            $obj['description'] = $myData['description'];
        }
        if (isset($myData['done'])) {
            $obj['done'] = $myData['done'];
        }
        if(!count($obj))
            return;
        $old_data = $task_dao->getById($myData['id']);
        $rows = $task_dao->updateByIdAndUserId($obj, $myData['id'], $my_user_id);
        if($rows) {
            if(isset($myData['description'])) {
                log_event($myData['id'], "modified", $myData['comment'], $old_data['description']);
            } if(isset($myData['done'])) {
                $done = $myData['done'] ? "done" : "undone";
                log_event($myData['id'], $done, $myData['comment'], null);
            }
        }
        break;
    case 'DELETE':
        $obj = array(
            'deleted' => true
        );
        $rows = $task_dao->updateByIdAndUserId($obj, $myData['id'], $my_user_id);
        if($rows) {
            log_event($myData['id'], "deleted", $myData['comment'], null);
        }
        break;
    default:
        http_send_status(405);
}
print_data_object();

function print_data_object()
{
    global $task_dao, $req_user_id, $user_id, $username;
    $data = $task_dao->getAllByUserIdNotDeleted($req_user_id);
    $dataObj = array(
        "user_id" => $user_id,
        "username" => $username,
        "tasks" => data2forest($data)
    );
    echo json_encode($dataObj, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
}

function data2tree($data, $parentId)
{
    $tree = array_values(array_filter($data, function ($value) use ($parentId) {
        return $value['parent_id'] == $parentId;
    }));
    foreach ($tree as $key => $node) {
        $children = data2tree($data, $node['id']);
        if (count($children))
            $tree[$key]['children'] = $children;
    }
    return $tree;
}

function data2forest($data)
{
    return data2tree($data, null);
}

function log_event($task_id, $type, $comment, $old_value) {
    global $my_user_id, $event_dao;
    $obj = array(
        "user_id" => $my_user_id,
        "task_id" => $task_id,
        "type" => $type,
        "comment" => $comment
    );
    if($old_value)
        $obj['old_value'] = $old_value;
    $event_dao->insert($obj);
}