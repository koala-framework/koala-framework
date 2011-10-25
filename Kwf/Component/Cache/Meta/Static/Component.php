<?php
class Kwf_Component_Cache_Meta_Static_Component extends Kwf_Component_Cache_Meta_Abstract
{
    protected $_componentClass;

    public function __construct($componentClass)
    {
        $this->_componentClass = $componentClass;
    }

    public function getComponentClass()
    {
        return $this->_componentClass;
    }

    public static function getDeleteDbId($row, $dbId)
    {
        return '';
    }
}