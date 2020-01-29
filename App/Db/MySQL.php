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

        if (!$this->connect) {
            $this->connect = mysqli_connect($this->host, $this->username, $this->password, $this->db_name);

            $mysql_errno = mysqli_connect_errno();
            if ($mysql_errno > 0) {
                $mysql_error = mysqli_connect_error();
                $message = "Mysql connect error: ($mysql_errno) $mysql_error";
                die($message);
            }

            mysqli_set_charset($this->connect, 'utf8');
        }

        return $this->connect;
    }

    public function query($query) {
        $result = mysqli_query($this->connect(), $query);
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

    public function insert(string $table_name, array $value) {

        $table_name = $this->escape($table_name);

        $columns = array_keys($value);
        $columns = array_map(function($item) {
            return $this->escape($item);
        }, $columns);

        $columns = implode(',', $columns);

        $values = array_map(function($item) {
            return $this->escape($item);
        }, $value);
        $values = '\'' . implode('\',\'', $values) . '\'';


        $query = "INSERT INTO $table_name($columns) VALUES ($values)";

        $this->query($query);

        return mysqli_insert_id($this->connect());
    }

    public function update(string $table_name, array $values, array $where = [])
    {
        $table_name = $this->escape($table_name);

        $set_data = [];
        foreach ($values as $key => $value) {
            $set_data[] = $this->escape($key) . ' = \'' . $this->escape($value) . '\'';
        }

        $set_data = implode(', ', $set_data);


        $where_data = [];
        foreach ($where as $key => $value) {
            $where_data[] = $this->escape($key) . ' = \'' . $this->escape($value) . '\'';
        }
        $query = "UPDATE $table_name SET $set_data";

        if (!empty($where_data)) {
            $where_data = implode(' AND ', $where_data);
            $query .= ' WHERE ' . $where_data;
        }

        $this->query($query);
    }

    public function delete(string $table_name, array $where = [])
    {
        $table_name = $this->escape($table_name);

        $where_data = [];
        foreach ($where as $key => $value) {
            $where_data[] = $this->escape($key) . ' = \'' . $this->escape($value) . '\'';
        }
        $query = "DELETE FROM $table_name";

        if (!empty($where_data)) {
            $where_data = implode(' AND ', $where_data);
            $query .= ' WHERE ' . $where_data;
        }

        $this->query($query);
    }

    public function escape(string $value) {
        return mysqli_real_escape_string($this->connect(), $value);
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

        $mysqli_errno = mysqli_errno($this->connect());
        if (!$mysqli_errno) {
            return true;
        }

        $mysqli_error = mysqli_error($this->connect());

        $message = "Mysql query error: ($mysqli_errno) $mysqli_error";
        throw new \Exception($message);
    }

}


/**
 *
 *
 */