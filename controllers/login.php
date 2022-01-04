<?php session_start();

use sonaro\DB;
use sonaro\Tasks;
use sonaro\Validation;

if (isset($_SESSION['user_id'])) {
    header('Location:/sonaro');
} else {
    $regInfo = '';
    $error = '';
    $filledData = '';
    $connection = DB::connect();
    $task = new Tasks($connection);

    if (isset($_POST['registerBtn'])) {
        $validation = Validation::validation($_POST);
        if (empty(implode('', $validation))) {
            $regInfo = $task->register($_POST);
        }
    }

    if (isset($_POST['loginBtn'])) {
        $error = $task->login($_POST);
    }

    if (isset($_POST)) {
        $filledData = $_POST;
    }

    require 'view/pages/login.view.php';
}
