<?php session_start();

use sonaro\DB;
use sonaro\Tasks;

header("Content-Type:application/json");

if (isset($_SESSION['user_id'])) {
    $connection = DB::connect();
    $task = new Tasks($connection);
    $users = $task->users();
    echo json_encode($users);
} else {
    header('Location:/sonaro');
}
