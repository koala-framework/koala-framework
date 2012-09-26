<?php
abstract class Kwf_Exception_Logger_Abstract
{
    public static function getInstance()
    {
        static $i;
        if (!isset($i)) {
            $logger = Kwf_Config::getValue('debug.exceptionLogger');
            $i = new $logger();
        }
        return $i;
    }

    abstract public function log(Kwf_Exception_Abstract $exception, $type, $content);
}
