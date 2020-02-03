<?php


namespace App\Controller;


use App\Service\RequestService;
use App\Service\UserService;

class User
{
    private function __construct()
    {
    }

    public static function login() {
        $login = RequestService::getStringFromPost('login');
        $password = RequestService::getStringFromPost('password');


        /**
         * @var $user \App\Model\User
         */
        $user = UserService::getUserByName($login);

        $error_msg = 'User not found or data is incorrect';

        if (is_null($user)) {
            echo $error_msg;
            exit;
        }

        if ($user->getPassword() !== $password) {
            echo $error_msg;
            exit;
        }

        $_SESSION['user_id'] = $user->getId();

        RequestService::redirect('/');
    }
}