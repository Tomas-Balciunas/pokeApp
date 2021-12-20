<?php session_start();

use sonaro\DB;
use sonaro\Tasks;

if (isset($_SESSION['user_id'])) {

    if (!empty($_POST)) {
        $id = $_SESSION['user_id'];
        $connection = DB::connect();
        $task = new Tasks($connection);
        $info = $task->updateUser($_POST, $id);
        echo json_encode($info);
    } else {
        header('Location:/sonaro/profile');
    }

} else {
    header('Location:/sonaro');
}
