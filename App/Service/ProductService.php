<?php


namespace App\Service;


use App\Model\Model;
use App\Model\Product;

class ProductService
{
    public function __construct()
    {
    }

    public function getCount() {
        $query = "SELECT COUNT(1) as count FROM products";
        /**
         * @var $result Model
         */
        $result = db()->fetchRow($query, Model::class);

        return (int) $result->getProperty('count') ?? 0;
    }

    /**
     * @param string|null $hash_key
     * @param integer $start
     * @param integer $limit
     * @return Product[]
     */
    public function getList(string $hash_key = null, int $start = 0, int $limit = 100): array {
        $query = "SELECT * FROM products ORDER BY id LIMIT $start, $limit";

        if (is_null($hash_key)) {
            $products = db()->fetchAll($query, Product::class);
        } else {
            $products = db()->fetchAllHash($query, $hash_key, Product::class);
        }

        $this->getFolderIdsForProducts($products);

        return $products;
    }

    /**
     * @param int $product_id
     * @return Product
     */
    public function getById(int $product_id): Product {
        $query = "SELECT * FROM products WHERE id = $product_id";

        $product = db()->fetchRow($query, Product::class);
        $this->getFolderIdsForProduct($product);

        return $product;
    }

    public function save(Product $product) {
        $data = [
            'name' => $product->getName(),
            'price' => $product->getPrice(),
            'amount' => $product->getAmount(),
            'description' => $product->getDescription(),
            'vendor_id' => $product->getVendorId(),
        ];


        $product_id = $product->getId();
        if ($product_id > 0) {
            db()->update('products', $data, [
                'id' => $product_id
            ]);
            $this->removeLinksWithFolders($product);
        } else {
            $product_id = db()->insert('products', $data);
        }

//        $folder_ids = $product->getFolderIds();
//        $product = $this->getById($product_id);
//        $product->removeAllFolders();
//
//        foreach ($folder_ids as $folder_id) {
//            $product->addFolderId($folder_id);
//        }

        $this->updateLinksWithFolders($product_id, $product->getFolderIds());

        return $this->getById($product_id);
    }

    private function removeLinksWithFolders(Product $product) {
        db()->delete('products_folders', [
            'product_id' => $product->getId(),
        ]);
    }

    private function updateLinksWithFolders(int $product_id, array $folder_ids) {
        $folder_ids = array_unique($folder_ids);

        foreach($folder_ids as $folder_id) {
            db()->insert('products_folders', [
                'product_id' => $product_id,
                'folder_id' => $folder_id
            ]);
        }
    }

    private function getFolderIdsForProduct(Product $product) {
        $product_id = $product->getId();

        $query = "SELECT folder_id FROM products_folders WHERE product_id = $product_id";
        $folder_ids = db()->fetchAll($query, Model::class);

        foreach ($folder_ids as $link) {
            $product->addFolderId($link->folder_id);
        }
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
            $links = db()->fetchAll($query, Model::class);

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