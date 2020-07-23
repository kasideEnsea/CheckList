<?php
if (!isset($_COOKIE["PHPSESSID"]) || !session_start() || !isset($_SESSION['id']))
    die("Вы не авторизованы!");
header('Content-Type: text/plain');
$user_id = $_SESSION['id'];

require("../database/task_dao.php");
$dao = new TaskDao();
$myData = json_decode(file_get_contents('php://input'), true);

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
        $dao->insert($obj);
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
        $dao->updateByIdAndUserId($obj, $myData['id'], $user_id);
        break;
    case 'DELETE':
        //ToDo - Залогировать это событие
        $myData = json_decode(file_get_contents('php://input'), true);
        $obj = array(
            'deleted' => true
        );
        $dao->updateByIdAndUserId($obj, $myData['id'], $user_id);
        break;
    default:
        http_send_status(405);
}
$data = $dao->getAllByUserIdNotDeleted($user_id);
echo json_encode(data2forest($data), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);

function getById($data, $id)
{
    foreach ($data as $value)
        if ($value['id'] == $id)
            return $value;
    return null;
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