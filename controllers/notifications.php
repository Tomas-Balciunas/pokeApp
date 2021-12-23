<?php session_start();

use sonaro\DB;
use sonaro\Tasks;

if (isset($_SESSION['user_id'])) {
    $id = $_SESSION['user_id'];
    $connection = DB::connect();
    $task = new Tasks($connection);
    
    echo json_encode($task->notifs($id));
}
