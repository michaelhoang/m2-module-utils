<?php

namespace Mike\Utils\Helper;
trait ConfigTrait
{
    public function getSystemConfig($field = '', $group = 'general', $section = 'design')
    {
        return $this->scopeConfig->getValue("$section/$group/$field", \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }
}
