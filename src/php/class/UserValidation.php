<?php

namespace vagrant\TheBoringSocial\php\class;

class UserValidation
{

    public static function validateAge($birthday, $age = 18)
    {
        if (is_string($birthday)) {
            $birthday = strtotime($birthday);
        }
        if (time() - $birthday < $age * 31536000) {
            return false;
        }
    }
}
