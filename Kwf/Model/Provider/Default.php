<?php
class Kwf_Model_Provider_Default extends Kwf_Model_Provider_Abstract
{
    public function findModels()
    {
        $ret = array();
        foreach (glob('models/*.php') as $m) {
            $m = str_replace('/', '_', substr($m, 7, -4));
            $reflectionClass = new ReflectionClass($m);
            if (!$reflectionClass->isAbstract() && is_instance_of($m, 'Kwf_Model_Interface')) {
                self::_findAllInstancesProcessModel($ret, $m);
            }
        }

        if (Kwf_Config::getValue('user.model')) {
            self::_findAllInstancesProcessModel($ret, Kwf_Config::getValue('user.model'));
        }

        //hardcoded models that always exist
        self::_findAllInstancesProcessModel($ret, 'Kwf_Util_Model_Welcome');
        self::_findAllInstancesProcessModel($ret, 'Kwf_Util_Model_Redirects');

        return $ret;
    }
}
