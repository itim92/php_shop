<?php


namespace App\Repository;


use App\Model\Model;
use App\Model\Product;

/**
 * Class ProductRepository
 * @package App\Repository
 *
 * @method Product find(int $id)
 */
class ProductRepository extends AbstractRepository
{

    protected $model = Product::class;

    protected function modifyResultList(array $result)
    {
        $result = parent::modifyResultList($result);
        $this->getFolderIdsForProducts($result);

        return $result;
    }

    /**
     * @param Product[] $products
     */
    private function getFolderIdsForProducts(array $products) {

        $product_ids = array_map(function($item) {
            /**
             * @var $item Product
             */
            return (int) $item->getId();
        }, $products);

        $product_ids = array_unique($product_ids);

        if (count($product_ids) > 0) {
            $product_ids = implode(',', $product_ids);
            $query = "SELECT * FROM products_folders WHERE product_id IN ($product_ids)";
            $links = $this->mySQL->fetchAll($query, Model::class);

            foreach ($links as $pair) {
                $product_id = $pair->product_id;
                $folder_id = $pair->folder_id;

                /**
                 * @todo Переделать связи, иначе на больших объемах данных будут тормоза
                 */
                foreach ($products as $product) {
                    if ($product->getId() != $product_id) {
                        continue;
                    }

                    $product->addFolderId($folder_id);
                }
            }
        }
    }
}