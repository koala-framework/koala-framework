<?php
class Vps_Soap_Client extends Zend_Soap_Client
{
    public function __call($name, $arguments)
    {
        $b = Vps_Benchmark::start('soapCall', $name);
        $ret = parent::__call($name, $arguments);
        if ($b) $b->stop();
        return $ret;
    }
}
