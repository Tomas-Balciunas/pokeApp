<?php session_start();

use sonaro\DB;
use sonaro\Tasks;

header("Content-Type:application/json");

if (isset($_SESSION['user_id'], $_POST['search'], $_POST['page'])) {
    $connection = DB::connect();
    $task = new Tasks($connection);
    $rowsRes = $task->rowCountSearch($_POST);
    $rows = $rowsRes['COUNT(*)'];
    $data = [];
    $itemsPerPage = 10;
    $pages = ceil($rows / $itemsPerPage);

    if (is_numeric($_POST['page'])) {
        $currentPage = (int) $_POST['page'];
    } else {
        $currentPage = 1;
    }

    $offset = ($currentPage - 1) * $itemsPerPage;
    $data['pages']['all'] = $pages;
    $data['pages']['current'] = $currentPage;
    $data['users'] = $users = $task->search($_POST, $offset, $itemsPerPage);
    echo json_encode($data);
} else {
    header('Location:/sonaro');
}
