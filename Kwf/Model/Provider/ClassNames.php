<?php
abstract class Kwf_Model_Provider_ClassNames extends Kwf_Model_Provider_Abstract
{
    protected $_modelClasses;

    public function findModels()
    {
        $ret = array();
        foreach ($this->_modelClasses as $modelClass) {
            self::_findAllInstancesProcessModel($ret, $modelClass);
        }
        return $ret;
    }
}
