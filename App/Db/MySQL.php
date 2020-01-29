<?php

namespace App\Db;

use App\Model\Product;

class MySQL
{
/**
 * устанавливать соединение с БД
 * выполнять запросы
 * отлавливать ошибки
 * возвращать результат (в разных форматах)
 * При каждом запросе, должен использовать раннее созданное соединение
 */

    private $host;
    private $username;
    private $password;
    private $db_name;

    private $connect;


    public function __construct(string $host, string $username, string $password, string $db_name)
    {
        $this->host = $host;
        $this->username = $username;
        $this->password = $password;
        $this->db_name = $db_name;
    }

    private function connect() {

        if ($this->connect) {
            return;
        }

        $this->connect = mysqli_connect($this->host, $this->username, $this->password, $this->db_name);

        $mysql_errno = mysqli_connect_errno();
        if ($mysql_errno > 0) {
            $mysql_error = mysqli_connect_error();
            $message = "Mysql connect error: ($mysql_errno) $mysql_error";
            die($message);
        }

        mysqli_set_charset($this->connect, 'utf8');
    }

    public function query($query) {
        $this->connect();

        $result = mysqli_query($this->connect, $query);
        $this->checkErrors();

        return $result;
    }

    public function fetchRow($query, string $class_name) {
        $result = $this->query($query);
        $this->checkModelClassExist($class_name);

        return mysqli_fetch_object($result, $class_name);
    }

    public function fetchAll($query, string $class_name) {
        $result = $this->query($query);

        $this->checkModelClassExist($class_name);

        $data = [];

        while($row = mysqli_fetch_object($result, $class_name)) {
            $data[] = $row;
        }

        return $data;
    }

    public function fetchAllHash(string $query, string $hash_key, string $class_name) {
        $result = $this->query($query);

        $data = [];

        $this->checkModelClassExist($class_name);

        while($row = mysqli_fetch_object($result, $class_name)) {
            /**
             * @var $row IModel
             */
//            if (!isset($row[$hash_key])) {
//
//            }

            $key = $row->getProperty($hash_key);
            $data[$key] = $row;
        }

        return $data;
    }

    private function checkModelClassExist(string $class_name) {
        $class_exist = class_exists($class_name);

        if ($class_exist) {
            $model_class = IModel::class;
            $cap_object = new $class_name;
            $is_model = in_array($model_class, class_implements($cap_object));

            if (!$is_model) {
                throw new \Exception("Class \"{$class_name}\" not implements \"{$model_class}\"");
            }
        } else {
            throw new \Exception("Class \"{$class_name}\" not exist");
        }
    }

    private function checkErrors() {
        $this->connect();

        $mysqli_errno = mysqli_errno($this->connect);
        if (!$mysqli_errno) {
            return true;
        }

        $mysqli_error = mysqli_error($this->connect);

        $message = "Mysql query error: ($mysqli_errno) $mysqli_error";
        throw new \Exception($message);
    }

}


/**
 *
 *
 */