<?php

namespace sonaro;

class Request
{
    public static function uri()
    {
        return str_replace("/sonaro", "", trim($_SERVER['REQUEST_URI']));
    }
}
