<?php


namespace App\Service;


use App\Model\User;

class UserService
{

    private static $salt = 'Fd@6k+7+FmhO';

    public static function generatePasswordHash(string $password) {
        return static::md5(static::md5($password));
    }

    private static function md5(string $str) {
        return md5($str . static::$salt);
    }

    public static function isEmailExist(string $email) {
        $email = db()->escape($email);
        $query = "SELECT * FROM users WHERE email = '$email'";
        $result = db()->fetchRow($query, User::class);

        return !is_null($result);
    }

    public static function save(User $user) {
        $id = $user->getId();


        if ($id) {
            $user = static::edit($user);
        } else {
            $user = static::create($user);
        }

        return $user;
    }

    public static function create(User $user) {
        $data = [
            'name' => $user->getName(),
            'email' => $user->getEmail(),
            'password' => $user->getPassword(),
        ];

        $user_id = db()->insert('users', $data);

        return static::getById($user_id);
    }

    public static function edit(User $user) {
        $id = $user->getId();

        if (!$id) {
            $message = 'User doesnt exist';
            throw new \Exception($message);
        }

        $data = [
            'name' => $user->getName(),
            'email' => $user->getEmail(),
            'password' => $user->getPassword(),
        ];

        db()->update('users', $data, [
            'id' => $id
        ]);

        return static::getById($id);
    }

    public static function getUserByName(string $username) {
        $username = db()->escape($username);

        $query = "SELECT * FROM users WHERE name = '$username'";

        return db()->fetchRow($query, User::class);
    }

    public static function getById(int $user_id) {
        $query = "SELECT * FROM users WHERE id = $user_id";

        return db()->fetchRow($query, User::class);
    }

}