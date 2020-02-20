<?php


namespace App\Repository;


use App\Model\User;

/**
 * Class UserRepository
 * @package App\Repository
 *
 * @method User find(int $id)
 * @method User[] findAll()
 * @method User[] findAllWithLimit(int $limit = 50, int $start = 0)
 */
class UserRepository extends AbstractRepository
{
    protected $model = User::class;

    public function findByName(string $name): User {
        $name = $this->mySQL->escape($name);

        $query = "SELECT * FROM users WHERE name = '$name'";

        return $this->mySQL->fetchRow($query, $this->model);
    }
}