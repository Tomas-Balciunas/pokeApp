<?php 

$router->define([
    '/' => 'controllers/home.php',
    '/login' => 'controllers/login.php',
    '/logout' => 'controllers/logout.php',
    '/profile' => 'controllers/profile.php',
    '/profile/update' => 'controllers/update-profile.php',
    '/user' => 'controllers/user-data.php',
    '/user_list' => 'controllers/user-list.php',
    '/poke' => 'controllers/poke.php',
    '/user_search' => 'controllers/user-search.php'
]);