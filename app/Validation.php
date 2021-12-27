<?php

namespace sonaro;

class Validation
{
    private static $errors = [];

    public static function validation($post)
    {
        $pw = ['pw1' => $post['registerPassword'], 'pw2' => $post['registerPasswordRepeat']];
        self::name($post['registerName']);
        self::lastName($post['registerLastname']);
        self::email($post['registerEmail']);
        self::password($pw);
        return self::$errors;
    }

    public static function updateValidation ($post) {
        $pw = ['pw1' => $post['updatePasswordNew'], 'pw2' => $post['updatePasswordNewRepeat']];
        self::lastName($post['updateLastname']);
        self::email($post['updateEmail']);
        self::password($pw);
        return self::$errors;
    }

    public static function importValidation ($name, $lastname, $email) {
        self::name($name);
        self::lastName($lastname);
        self::email($email);
        return self::$errors;
    }

    public static function name($e)
    {
        $val = preg_match('/^[a-zA-ZąčęėįšųūžĄČĘĖĮŠŲŪŽ]{3,25}$/', $e);

        if (empty($e)) {
            Validation::$errors['name'] = 'Name field is empty';
        } elseif (!$val) {
            Validation::$errors['name'] = 'Name can only contain letters, from 3 to 25 symbols in length';
        } else {
            Validation::$errors['name'] = '';
        }
    }

    public static function lastName($e)
    {
        $val = preg_match('/^[a-zA-ZąčęėįšųūžĄČĘĖĮŠŲŪŽ]{3,25}$/', $e);

        if (empty($e)) {
            Validation::$errors['lastName'] = 'Last name field is empty';
        } elseif (!$val) {
            Validation::$errors['lastName'] = 'Last name can only contain letters, form 3 to 25 symbols in length';
        } else {
            Validation::$errors['lastName'] = '';
        }
    }

    public static function email($e)
    {
        if (empty($e)) {
            Validation::$errors['email'] = 'Email field is empty';
        } else if (!filter_var($e, FILTER_VALIDATE_EMAIL)) {
            Validation::$errors['email'] = 'Please enter a correct email (example@example.com)';
        } else {
            Validation::$errors['email'] = '';
        }
    }

    public static function password($e)
    {
        $val = preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)[a-zA-Z\d]{6,20}$/', $e['pw1']);

        if (empty($e['pw1']) or empty($e['pw2'])) {
            Validation::$errors['password'] = 'Both password fields must be filled';
        } elseif ($e['pw1'] !== $e['pw2']) {
            Validation::$errors['password'] = 'Passwords do not match';
        } elseif (!$val) {
            Validation::$errors['password'] = 'Password must contain from 6 to 20 symbols, at least one uppercase letter and one number';
        } else {
            Validation::$errors['password'] = '';
        }
    }
}
