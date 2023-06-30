<?php

namespace Mike\Utils\Helper;
trait ObjectManagerTrait
{
    /**
     * @return \Magento\Framework\App\ObjectManager
     */
    public function getOm()
    {
        return \Magento\Framework\App\ObjectManager::getInstance();
    }
}
