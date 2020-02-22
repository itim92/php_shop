<?php


namespace App\Model;


use App\Model\Interfaces\IEntity;

abstract class AbstractEntity implements IEntity
{

    /**
     * @var
     */
    protected $table_name;

    /**
     * @var string
     */
    protected $primary_key = 'id';

    /**
     * @var int
     */
    protected $id;

    /**
     * @var array
     */
    protected $table_fields;

    /**
     * @var array
     */
    protected $immutable_table_fields;

    public function getColumnValue(string $key): string {
        return (string) $this->$key;
    }

    public function getPrimaryKey(): string
    {
        return $this->primary_key;
    }

    public function getPrimaryKeyValue(): string
    {
        return $this->{$this->getPrimaryKey()};
    }

    public function getTableName(): string
    {
        return $this->table_name;
    }

    public function getColumnsForInsert(): array
    {
        return $this->table_fields;
    }

    public function getColumnsForUpdate(): array
    {
        return array_diff_assoc(
            $this->getColumnsForInsert(),
            $this->immutable_table_fields
        );
    }


}