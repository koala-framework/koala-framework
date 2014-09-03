<?php
class Kwc_Basic_Text_ChildComponentsModelFactory extends Kwf_Model_Factory_Abstract
{
    public static function getModelInstance($config)
    {
        return Kwc_Basic_Text_ModelFactory::getModelInstance(array(
                'componentClass' => $config['componentClass']
            ))->getDependentModel('ChildComponents');
    }
}
