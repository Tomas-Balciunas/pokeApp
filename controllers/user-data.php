<?php session_start();

use sonaro\DB;
use sonaro\Tasks;

header("Content-Type:application/json");

if (isset($_SESSION['user_id'])) {
    $id = $_SESSION['user_id'];
    $connection = DB::connect();
    $task = new Tasks($connection);
    $user = $task->fetchProfile($id);
    echo json_encode($user);
} else {
    header('Location:/sonaro');
}