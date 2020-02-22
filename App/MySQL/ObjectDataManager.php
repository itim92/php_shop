<?php


namespace App\MySQL;

use App\MySQL\Interfaces\ITableRow;
use App\MySQL\Exceptions\GivenClassNotImplementerITableRowException;
use App\MySQL\Exceptions\QueryException;
use App\MySQL\Interfaces\IArrayDataManager;
use App\MySQL\Interfaces\IConnection;
use App\MySQL\Interfaces\IObjectDataManager;
use mysqli_result;

class ObjectDataManager implements IObjectDataManager
{

    /**
     * @var IConnection
     */
    protected $connection;

    /**
     * @var IArrayDataManager
     */
    protected $arrayDataManager;


    public function __construct(IConnection $connection, IArrayDataManager $arrayDataManager)
    {
        $this->connection = $connection;
        $this->arrayDataManager = $arrayDataManager;
    }

    /**
     * @param string $query
     * @param string $class_name
     * @return ITableRow
     * @throws GivenClassNotImplementerITableRowException
     * @throws QueryException
     */
    public function fetchRow(string $query, string $class_name): ITableRow
    {
        $this->isITableRowClass($class_name);

        $result = $this->query($query);

        /**
         * @var ITableRow $row
         */
        $row = mysqli_fetch_object($result, $class_name);

        return $row;
    }

    /**
     * @param string $query
     * @param string $class_name
     *
     * @return ITableRow[]
     *
     * @throws GivenClassNotImplementerITableRowException
     * @throws QueryException
     */
    public function fetchAll(string $query, string $class_name): array
    {
        $this->isITableRowClass($class_name);

        $result = $this->query($query);

        $data = [];

        while($row = mysqli_fetch_object($result, $class_name)) {
            /**
             * @var ITableRow $row
             */

            $data[] = $row;
        }

        return $data;
    }


    /**
     * @param string $query
     * @param string $class_name
     * @param string $hash_key
     * @return ITableRow[]
     * @throws GivenClassNotImplementerITableRowException
     * @throws QueryException
     */
    public function fetchAllHash(string $query, string $hash_key, string $class_name): array {
        $this->isITableRowClass($class_name);

        $result = $this->query($query);

        $data = [];
        while($row = mysqli_fetch_object($result, $class_name)) {
            /**
             * @var $row ITableRow
             */

            $key = $row->getColumnValue($hash_key);

            if (is_null($key)) {
                continue;
            }

            $data[$key] = $row;
        }

        return $data;
    }

    public function save(ITableRow $row): ITableRow
    {
        if ($row->getPrimaryKeyValue() > 0) {
            return $this->update($row);
        }

        return $this->insert($row);
    }

    protected function update(ITableRow $row): ITableRow {
        $data = [];

        $this->arrayDataManager->update($row->getTableName(), $data, [
            $row->getPrimaryKey() => $row->getPrimaryKeyValue(),
        ]);
    }




    //    public function save(AbstractModel $model): AbstractModel
//    {
//        if ($model->getProperty('id') > 0) {
//            return $this->update($model);
//        }
//
//        return $this->insert($model);
//    }

    public function saveMany(string $table_name, array $rows): array
    {
        return [];
    }

    public function delete(ITableRow $row): int
    {
        return 0;
    }



//
//    /**
//     * @param string $table_name
//     * @param array $value
//     * @return int
//     * @throws QueryException
//     */
//    public function insert(string $table_name, array $value): int {
//
//        $table_name = $this->escape($table_name);
//
//        $columns = array_keys($value);
//        $columns = array_map(function($item) {
//            return $this->escape($item);
//        }, $columns);
//
//        $columns = implode(',', $columns);
//
//        $values = array_map(function($item) {
//            return $this->escape($item);
//        }, $value);
//        $values = '\'' . implode('\',\'', $values) . '\'';
//
//
//        $query = "INSERT INTO $table_name($columns) VALUES ($values)";
//
//        $this->query($query);
//
//        return mysqli_insert_id($this->getConnect());
//    }
//
//    /**
//     * @param string $table_name
//     * @param array $values
//     * @return array
//     * @throws QueryException
//     */
//    public function insertMany(string $table_name, array $values): array
//    {
//        $inserted_ids = [];
//
//        foreach($values as $value) {
//            $inserted_ids[] = $this->insert($table_name, $value);
//        }
//
//        return $inserted_ids;
//    }
//
//    /**
//     * @param string $table_name
//     * @param array $value
//     * @param array $where
//     * @return int
//     * @throws QueryException
//     */
//    public function update(string $table_name, array $value, array $where = []): int
//    {
//        $table_name = $this->escape($table_name);
//
//        $set_data = [];
//        foreach ($value as $key => $param_value) {
//            $set_data[] = $this->escape($key) . ' = \'' . $this->escape($param_value) . '\'';
//        }
//
//        $set_data = implode(', ', $set_data);
//
//
//        $where_data = [];
//        foreach ($where as $key => $param_value) {
//            $where_data[] = $this->escape($key) . ' = \'' . $this->escape($param_value) . '\'';
//        }
//        $query = "UPDATE $table_name SET $set_data";
//
//        if (!empty($where_data)) {
//            $where_data = implode(' AND ', $where_data);
//            $query .= ' WHERE ' . $where_data;
//        }
//
//
//        $this->query($query);
//
//        return mysqli_affected_rows($this->getConnect());
//    }
//
//    /**
//     * @param string $table_name
//     * @param array $where
//     * @return int
//     * @throws QueryException
//     */
//    public function delete(string $table_name, array $where = []): int
//    {
//        $table_name = $this->escape($table_name);
//
//        $where_data = [];
//        foreach ($where as $key => $value) {
//            $where_data[] = $this->escape($key) . ' = \'' . $this->escape($value) . '\'';
//        }
//        $query = "DELETE FROM $table_name";
//
//        if (!empty($where_data)) {
//            $where_data = implode(' AND ', $where_data);
//            $query .= ' WHERE ' . $where_data;
//        }
//
//        $this->query($query);
//
//        return mysqli_affected_rows($this->getConnect());
//    }

    public function escape(string $value) {
        return mysqli_real_escape_string($this->getConnect(), $value);
    }

    /**
     * @return IConnection
     */
    protected function getConnection() {
        return $this->connection;
    }

    /**
     * @return mixed
     */
    protected function getConnect() {
        return $this->getConnection()->getConnect();
    }

    /**
     * @param string $query
     * @return bool|mysqli_result
     *
     * @throws QueryException
     */
    protected function query(string $query) {
        $result = mysqli_query($this->getConnect(), $query);
        $this->checkErrors();

        return $result;
    }

    /**
     * @throws QueryException
     */
    protected function checkErrors() {
        $mysqli_errno = mysqli_errno($this->getConnect());

        if (!$mysqli_errno) {
            return;
        }

        $mysqli_error = mysqli_error($this->getConnect());

        $message = "Mysql query error: ($mysqli_errno) $mysqli_error";

        throw new QueryException($message);
    }


    /**
     * @param string $class_name
     * @throws GivenClassNotImplementerITableRowException
     */
    private function isITableRowClass(string $class_name) {
        $is_class_exists = class_exists($class_name);
        $class_implements = class_implements($class_name);
        $is_class_implements = in_array(ITableRow::class, $class_implements);

        if ($is_class_exists && $is_class_implements) {
            return;
        }

        $message = "$class_name not implemented ITableRow";
        throw new GivenClassNotImplementerITableRowException($message);
    }


}