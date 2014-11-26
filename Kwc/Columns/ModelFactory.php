<?php
class Kwc_Columns_ModelFactory extends Kwf_Model_Factory_Abstract
{
    public static function getModelInstance($config)
    {
        $componentClass = $config['componentClass'];
        static $models = array();
        if (!isset($models[$componentClass])) {
            $m = Kwc_Abstract::getSetting($componentClass, 'childModel');
            $models[$componentClass] = new $m(array('componentClass' => $componentClass));
        }
        return $models[$componentClass];
    }
}
