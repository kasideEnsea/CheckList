<?php
if (!isset($_COOKIE["PHPSESSID"]) || !session_start() || !isset($_SESSION['id']))
    die("Вы не авторизованы!");
header('Content-Type: text/plain');
$my_user_id = $_SESSION['id'];
$req_user_id = isset($_GET['id']) ? $_GET['id'] : $my_user_id;

require("../database/task_dao.php");
require("../database/user_dao.php");
$task_dao = new TaskDao();
$user_dao = new UserDao();
$user_id = $user_dao->getById($req_user_id)['id'];
$username = $user_dao->getById($req_user_id)['name'];

$myData = json_decode(file_get_contents('php://input'), true);
if($req_user_id != $my_user_id) {
    get_data_object($task_dao, $req_user_id, $user_id, $username);
    exit();
}
switch ($_SERVER['REQUEST_METHOD']) {
    case 'GET':
        //Do nothing
        break;
    case 'POST':
        //ToDo - Залогировать это событие
        $obj = array(
            'id' => 0,
            'user_id' => $_SESSION['id'],
            'description' => $myData['description'],
            'parent_id' => $myData['parent_id']
        );
        $task_dao->insert($obj);
        break;

    case 'PUT':
        $myData = json_decode(file_get_contents('php://input'), true);
        $obj = [];
        if (isset($myData['description'])) {
            //ToDo - Залогировать это событие
            $obj['description'] = $myData['description'];
        }
        if (isset($myData['done'])) {
            //ToDo - Залогировать это событие
            $obj['done'] = $myData['done'];
        }
        $task_dao->updateByIdAndUserId($obj, $myData['id'], $my_user_id);
        break;
    case 'DELETE':
        //ToDo - Залогировать это событие
        $myData = json_decode(file_get_contents('php://input'), true);
        $obj = array(
            'deleted' => true
        );
        $task_dao->updateByIdAndUserId($obj, $myData['id'], $my_user_id);
        break;
    default:
        http_send_status(405);
}
get_data_object($task_dao, $req_user_id, $user_id, $username);

function get_data_object(TaskDao $task_dao, $req_user_id, $user_id, $username)
{
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