<?php session_start();

if (isset($_SESSION['user_id'])) {
    require 'view/pages/home.view.php';
} else {
    header('Location:/sonaro/login');
}
