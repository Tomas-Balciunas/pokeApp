<?php session_start();

use sonaro\DB;
use sonaro\Poke;

if (isset($_SESSION['user_id']) && !empty($_POST)) {
    $info = '';
    $id = $_SESSION['user_id'];
    $connection = DB::connect();
    $task = new Poke($connection);

    if ($id == $_POST['id']) {
        $info = 'You cannot poke yourself!';
        echo json_encode($info);

    } elseif (isset($_SESSION['time']) && $_SESSION['time'] + 15 >= time()) {
        $info = 'You can only poke once every 15 seconds!';
        echo json_encode($info);

    } else {
        $info = $task->poke($id, $_POST);
        $_SESSION['time'] = time();
        echo json_encode($info);
    }
} else {
    header('Location:/sonaro');
}
