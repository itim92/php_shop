<?php


namespace App\Model;


use App\Db\IModel;

class AbstractModel implements IModel
{

    /**
     * @var
     */
    protected $table_name;

    public function getProperty(string $key) {
        return $this->$key;
    }
}