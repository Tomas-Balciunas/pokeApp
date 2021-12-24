<?php session_start();

use sonaro\DB;
use sonaro\Tasks;

if (isset($_SESSION['user_id'])) {
    if (isset($_POST['import'])) {
        $connection = DB::connect();
        $task = new Tasks($connection);
        $info = $task->import();
    }

    require 'view/pages/data.view.php';
} else {
    header('Location:/sonaro/login');
}