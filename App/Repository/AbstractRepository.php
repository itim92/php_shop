<?php


namespace App\Repository;


use App\Db\MySQL;
use App\Model\AbstractModel;
use App\Model\Model;

abstract class AbstractRepository
{

    /**
     * @var string
     */
    protected $model;

    /**
     * @var MySQL
     */
    protected $mySQL;

    protected $table_name;

    public function __construct(MySQL $mySQL)
    {
        if (!class_exists($this->model) || !in_array(AbstractModel::class, class_parents($this->model))) {
            throw new \Exception('Model should be a Model');
        }

        $this->table_name = $this->getTableName();
        $this->mySQL = $mySQL;
    }

    public function findAll() {
        $query = "SELECT * FROM " . $this->table_name;

        $result = $this->mySQL->fetchAllHash($query, 'id', $this->model);

        return $this->modifyResultList($result);

    }

    public function findAllWithLimit(int $limit = 50, int $start = 0) {

        $query = "SELECT * FROM " . $this->table_name . " LIMIT $start, $limit";

        $result = $this->mySQL->fetchAllHash($query, 'id', $this->model);

        return $this->modifyResultList($result);
    }


    public function getCount() {
        $query = "SELECT COUNT(1) as count FROM " . $this->table_name;

        /**
         * @var $result Model
         */
        $result = $this->mySQL->fetchRow($query, Model::class);

        return (int) $result->getProperty('count') ?? 0;
    }

    private function getTableName() {
        $model = new $this->model;
        $object = new \ReflectionObject($model);
        $property = $object->getProperty('table_name');
        $property->setAccessible(true);

        return $property->getValue($model);
    }

    protected function modifyResultList(array $result) {
        return $result;
    }
}