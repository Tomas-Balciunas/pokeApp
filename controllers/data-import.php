<?php session_start();

use sonaro\DB;
use sonaro\Import;

if (isset($_SESSION['user_id'])) {
    if (isset($_POST['import'])) {
        $connection = DB::connect();
        $task = new Import($connection);
        $info = $task->import();
    }

    if (isset($_POST['importPokes'])) {
        $connection = DB::connect();
        $task = new Import($connection);
        $info = $task->importPokes();
    }

    if (isset($_POST['generate'])) {
        $connection = DB::connect();
        $task = new Import($connection);
        $info = $task->generatePokes();
    }

    require 'view/pages/data.view.php';
} else {
    header('Location:/sonaro/login');
}