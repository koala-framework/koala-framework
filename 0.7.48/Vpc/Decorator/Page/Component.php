<?php
class Vpc_Decorator_Page_Component extends Vpc_Abstract
{
    public static function getSettings()
    {
        return array_merge(parent::getSettings(), array(
            'componentName' => 'Testdecorator',
            'tablename'     => 'Vpc_Decorator_Page_Model'
        ));
    }
}
