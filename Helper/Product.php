<?php

namespace Mike\Utils\Helper;

class Product
{
    use \Mike\Utils\Helper\ObjectManagerTrait;
    use \Mike\Utils\Helper\LoggerTrait;

    const DELETE_FAIL_MSG = "Has problem when delete products.";

    protected $logFile;

    public function __construct()
    {
        $this->logFile = BP . "/var/log/product-" . date("Ymd") . ".log";
    }

    public function deleteProductBySkus($skus, $deleteChildren = false)
    {
        $deletedIds = [];
        foreach ($skus as $sku) {
            /** @var \Magento\Catalog\Model\Product $product */
            $product = $this->getProductBySku($sku);
            if (!$product->getId()) {
                continue;
            }
            $deletedIds[] = $product->getId();
        }

        return $this->deleteProductByIds($deletedIds, true);
    }

    public function deleteProductByIds($ids, $deleteChildren = false)
    {
        $deletedIds = $ids;
        if ($deleteChildren) {
            foreach ($ids as $id) {
                $product = $this->getProductById($id);
                $childrenProduct = $this->getChildrenProduct($product);
                foreach ($childrenProduct as $childProduct) {
                    $deletedIds[] = $childProduct->getId();
                }
                $childrenProduct = [];
            }
        }

        # Delete directly
        $deletedIds = array_unique($deletedIds);
        return $this->deleteProducts($deletedIds);
    }

    public function deleteProducts($ids)
    {
        # Using zend to delete
        $resource = $this->getOm()->get(\Magento\Framework\App\ResourceConnection::class);

        /** @var \Magento\Framework\DB\Adapter\AdapterInterface $connection */
        $connection = $resource->getConnection();

        /** @var \Magento\Framework\Model\ResourceModel\Db\TransactionManager $transactionManager */
        $transactionManager = $this->getOm()->get(\Magento\Framework\Model\ResourceModel\Db\TransactionManagerInterface::class);

        /** @var \Magento\Framework\DB\Adapter\AdapterInterface $connection */
        $connection = $transactionManager->start($connection);

        try {
            # Delete
            foreach ($ids as $id) {
                $cond["entity_id=?"] = $id;

//                $connection->delete('catalog_product_entity', $cond);
            }

            $transactionManager->commit();

        } catch (\Exception $e) {
            $transactionManager->rollBack();
            $this->log(static::DELETE_FAIL_MSG, $this->logFile);
            $this->log($e->getMessage(), $this->logFile);
            throw $e;
        }

        return $ids;
    }

    /**
     * @param \Magento\Catalog\Model\Product $product
     * @return \Magento\Catalog\Api\Data\ProductInterface[]
     */
    public function getChildrenProduct($product)
    {
        return $product->getTypeInstance()->getUsedProducts($product);
    }

    /**
     * @param int $id
     * @return mixed
     */
    public function getProductById(int $id)
    {
        /** @var \Magento\Framework\App\ObjectManager $om */
        $om = $this->getOm();
        return $om->get(\Magento\Catalog\Model\ProductRepository::class)->getById($id);
    }

    /**
     * @param string $sku
     * @return mixed
     */
    public function getProductBySku(string $sku)
    {
        /** @var \Magento\Framework\App\ObjectManager $om */
        $om = $this->getOm();
        return $om->get(\Magento\Catalog\Model\ProductRepository::class)->get($sku);
    }
}

