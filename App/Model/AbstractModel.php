<?php


namespace App\Model;


use App\Db\IModel;

class AbstractModel implements IModel
{

    /**
     * @var
     */
    protected $table_name;

    /**
     * @var array
     */
    protected $table_fields;

    /**
     * @var array
     */
    protected $immutable_table_fields;

    public function getProperty(string $key) {
        return $this->$key;
    }

    public function getTableFields(): array {
        return $this->table_fields;
    }

    public function getImmutableTableFields(): array {
        return $this->immutable_table_fields;
    }
}