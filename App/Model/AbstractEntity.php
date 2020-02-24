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

    public function getColumnValue(string $key): ?string {
        return (string) $this->$key;
    }

    public function getPrimaryKey(): string
    {
        return $this->primary_key;
    }

    public function getPrimaryKeyValue(): ?string
    {
        return $this->{$this->getPrimaryKey()};
    }

    public function getTableName(): string
    {
        return $this->table_name;
    }

    public function getColumnsForInsert(): array
    {
        $columns = $this->getDbColumns();
        
//        echo '<pre>'; var_dump($this->table_fields); echo '</pre>'; exit;

        return $this->getColumnsValues($columns);
    }

    public function getColumnsForUpdate(): array
    {

        $columns = array_diff_assoc(
            $this->getDbColumns(),
            $this->getImmutableDbColumns()
        );

        return $this->getColumnsValues($columns);
    }

    protected function getColumnsValues(array $columns) {
        $data = [];

        foreach ($columns as $field) {
            $data[$field] = $this->{$field};
        }

        return $data;
    }

    protected function getDbColumns() {
        $columns = [];

        $reflection_object = new \ReflectionObject($this);
        $properties = $reflection_object->getProperties();
        foreach ($properties as $property) {
            /**
             * @var $property \ReflectionProperty
             */

            $property_doc_comment = $property->getDocComment();
            if (strpos($property_doc_comment, '@DbColumn') !== false) {
                $columns[] = $property->getName();
            }
        }

        return $columns;
    }

    protected function getImmutableDbColumns() {
        $columns = [];

        $reflection_object = new \ReflectionObject($this);
        $properties = $reflection_object->getProperties();
        foreach ($properties as $property) {
            /**
             * @var $property \ReflectionProperty
             */

            $property_doc_comment = $property->getDocComment();
            if (strpos($property_doc_comment, '@DbColumn(immutable') !== false) {
                $columns[] = $property->getName();
            }
        }

        return $columns;
    }

    public function offsetExists($offset)
    {
        $getter_name = $this->getGetterName($offset);

        return method_exists($this, $getter_name);
    }

    public function offsetGet($offset)
    {
        $getter_name = $this->getGetterName($offset);

        return $this->{$getter_name}();
    }

    public function offsetSet($offset, $value)
    {
        $setter_name = $this->getSetterName($offset);

        $this->{$setter_name}($value);
    }

    public function offsetUnset($offset)
    {
//        $setter_name = $this->getSetterName($offset);
//
//        $this->{$setter_name}(null);
    }


    protected function getGetterName(string $property_name) {
        $property_name_chunks = explode('_', $property_name);

        $property_name_chunks = array_map(function($item) {
            return ucfirst($item);
        }, $property_name_chunks);

        return 'get' . implode('', $property_name_chunks);
    }

    protected function getSetterName(string $property_name) {
        $property_name_chunks = explode('_', $property_name);

        $property_name_chunks = array_map(function($item) {
            return ucfirst($item);
        }, $property_name_chunks);

        return 'set' . implode('', $property_name_chunks);
    }


}