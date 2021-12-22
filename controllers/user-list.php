<?php session_start();

use sonaro\DB;
use sonaro\Tasks;

header("Content-Type:application/json");

if (isset($_SESSION['user_id'], $_POST['page'])) {
    $id = $_SESSION['user_id'];
    $connection = DB::connect();
    $task = new Tasks($connection);
    $data = [];

    $rowsRes = $task->rowCount();
    $rows = $rowsRes['COUNT(*)'];
    $itemsPerPage = 5;
    $pages = ceil($rows / $itemsPerPage);

    if (is_numeric($_POST['page'])) {
        $currentPage = (int) $_POST['page'];
    } else {
        $currentPage = 1;
    }

    $offset = ($currentPage - 1) * $itemsPerPage;
    $data['data']['pages']['all'] = $pages;
    $data['data']['pages']['current'] = $currentPage;
    $data['data']['data'] = $task->users($offset, $itemsPerPage);
    $data['data']['notifs'] = $task->notifs($id);
    echo json_encode($data);

} else {
    header('Location:/sonaro');
}
