<?php


namespace App\Repository;


use App\Db\MySQL;
use App\Model\AbstractEntity;
use App\Model\AbstractModel;
use App\Model\Model;
use App\MySQL\Exceptions\GivenClassNotImplementerITableRowException;
use App\MySQL\Exceptions\QueryException;
use App\MySQL\Interfaces\ITableRow;
use App\MySQL\ObjectDataManager;

abstract class AbstractRepository
{

    /**
     * @var string
     */
    protected $model;

    /**
     * @var ObjectDataManager
     */
    protected $odm;

    protected $table_name;

    /**
     * AbstractRepository constructor.
     * @param ObjectDataManager $odm
     * @throws \Exception
     */
    public function __construct(ObjectDataManager $odm)
    {
        if (!class_exists($this->model) || !in_array(AbstractEntity::class, class_parents($this->model))) {
            throw new \Exception('Model should be a AbstractEntity');
        }

        $this->table_name = $this->getTableName();
        $this->odm = $odm;
    }
//
//    public function save(AbstractModel $model): AbstractModel
//    {
//        if ($model->getProperty('id') > 0) {
//            return $this->update($model);
//        }
//
//        return $this->insert($model);
//    }
//
//
//    /**
//     * @param AbstractModel $model
//     * @return AbstractModel
//     */
//    public function insert(AbstractModel $model): AbstractModel {
//
//        $data = $this->getModelInsertData($model);
//
//        $last_insert_id = $this->odm->insert($this->table_name, $data);
//
//        return $this->find($last_insert_id);
//    }
//
//    protected function getModelInsertData(AbstractModel $model) {
//        $table_fields = $model->getTableFields();
//        $data = [];
//
//        foreach ($table_fields as $field) {
//            $data[$field] = $model->getProperty($field);
//        }
//
//        return $data;
//    }
//
//    /**
//     * @param AbstractModel $model
//     * @return AbstractModel
//     */
//    public function update(AbstractModel $model): AbstractModel {
//        $data = $this->getModelUpdateData($model);
//        $id = $model->getProperty('id');
//
//
//        $this->odm->update($this->table_name, $data, [
//            'id' => $id,
//        ]);
//
//        return $this->find($id);
//
////        $this->mySQL->update($this->table_name, $model);
////
////        return $this->find($id);
//    }
//
//    protected function getModelUpdateData(AbstractModel $model) {
//        $table_fields = $model->getTableFields();
//        $immutable_table_fields = $model->getImmutableTableFields();
//
//        $data = [];
//
//        foreach ($table_fields as $field) {
//            if (in_array($field, $immutable_table_fields)) {
//                continue;
//            }
//
//            $data[$field] = $model->getProperty($field);
//        }
//
//        return $data;
//    }
//
//    public function delete(AbstractModel $model) {
//        $id = $model->getProperty('id');
//
//        $this->odm->delete($this->table_name, [
//            'id' => $id,
//        ]);
//    }

    /**
     * @param int $id
     * @return mixed
     * @throws GivenClassNotImplementerITableRowException
     * @throws QueryException
     */
    public function find(int $id) {
        $query = "SELECT * FROM " . $this->table_name . " WHERE id = $id";

        $result = $this->odm->fetchRow($query, $this->model);

        return $this->modifyResultItem($result);

    }


    /**
     * @return mixed
     */
    public function create() {
        return new $this->model;
    }

    /**
     * @param int $id
     * @return mixed
     * @throws GivenClassNotImplementerITableRowException
     * @throws QueryException
     */
    public function findOrCreate(int $id) {
        if ($id > 0) {
            return $this->find($id);
        }

        return $this->create();
    }

    /**
     * @return array
     * @throws GivenClassNotImplementerITableRowException
     * @throws QueryException
     */
    public function findAll() {
        $query = "SELECT * FROM " . $this->table_name;

        $result = $this->odm->fetchAllHash($query, 'id', $this->model);

        return $this->modifyResultList($result);

    }

    /**
     * @param int $limit
     * @param int $start
     * @return array
     * @throws GivenClassNotImplementerITableRowException
     * @throws QueryException
     */
    public function findAllWithLimit(int $limit = 50, int $start = 0) {

        $query = "SELECT * FROM " . $this->table_name . " LIMIT $start, $limit";

        $result = $this->odm->fetchAllHash($query, 'id', $this->model);

        return $this->modifyResultList($result);
    }


    /**
     * @return int
     * @throws GivenClassNotImplementerITableRowException
     * @throws QueryException
     */
    public function getCount() {
        $query = "SELECT COUNT(1) as count FROM " . $this->table_name;

        /**
         * @var $result Model
         */
        $result = $this->odm->fetchRow($query, Model::class);

        return (int) $result->getColumnValue('count') ?? 0;
    }

    /**
     * @return mixed
     * @throws \ReflectionException
     */
    private function getTableName() {
        $model = new $this->model;
        $object = new \ReflectionObject($model);
        $property = $object->getProperty('table_name');
        $property->setAccessible(true);

        return $property->getValue($model);
    }

    protected function modifyResultItem(AbstractEntity $item) {
        $list = [
            0 => $item,
        ];

        $result = $this->modifyResultList($list);

        return $result[0];
    }

    protected function modifyResultList(array $result) {
        return $result;
    }
}