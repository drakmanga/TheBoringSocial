<?php

namespace vagrant\TheBoringSocial\php\class;

class Logout
{
    public static function logout()
    {
        session_unset();
        session_destroy();

        header("Location: ../php/login.php");
        die();
    }
}
