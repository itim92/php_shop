<?php


namespace App\Model;

class Folder extends AbstractEntity
{
    protected $table_name = 'folders';

    protected $table_fields = [
        'id',
        'name',
    ];

    protected $immutable_table_fields = [
        'id',
    ];

    /**
     * @var int
     *
     * @DbColumn()
     */
    protected $id = 0;

    /**
     * @var string
     */
    protected $name = '';

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName(string $name): void
    {
        $this->name = $name;
    }

}