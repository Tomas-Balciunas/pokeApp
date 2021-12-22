<?php session_start();

use sonaro\DB;
use sonaro\Tasks;
use sonaro\Validation;

if (isset($_SESSION['user_id'])) {
    if (!empty($_POST)) {
        $validation = Validation::updateValidation($_POST);
        $id = $_SESSION['user_id'];
        $connection = DB::connect();
        $task = new Tasks($connection);
        $return = [];

        if (empty(implode('', $validation))) {
            $info = $task->updateUser($_POST, $id);
            $return['vali'] = '';
            $return['info'] = $info;
            echo json_encode($return);
        } else {
            $return['info'] = '';
            $return['vali'] = $validation;
            echo json_encode($return);
        }

    } else {
        header('Location:/sonaro/profile');
    }
} else {
    header('Location:/sonaro');
}
