<?php

namespace Mike\Utils\Helper;
trait LoggerTrait
{
    public function log($message = '', $directory = '')
    {
        if (empty($directory)) {
            $directory = BP . "/var/log/utils-" . date("Ymd") . ".log";
        }

        /**
         * @todo Switch to laminas if upgrade to Magento 2.3.*
         */
        if (class_exists(\Zend_Log_Writer_Stream::class)) {
            # For 2.4+
            $writer = new \Zend_Log_Writer_Stream($directory);
            $logger = new \Zend_Log();
        } elseif (class_exists(\Laminas\Log\Writer\Stream::class)) {
            $writer = new \Laminas\Log\Writer\Stream($directory);
            $logger = new \Laminas\Log\Logger();
        } else {
            $writer = new \Zend\Log\Writer\Stream($directory);
            $logger = new \Zend\Log\Logger();
        }

        $logger->addWriter($writer);

        $logger->info($message);
    }
}
