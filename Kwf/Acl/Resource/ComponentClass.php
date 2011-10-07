<?php
class Kwf_Acl_Resource_ComponentClass extends Zend_Acl_Resource
    implements Kwf_Acl_Resource_ComponentClass_Interface
{
    protected $_componentClass;

    public function __construct($componentClass)
    {
        $this->_componentClass = $componentClass;
        parent::__construct('kwc_'.$componentClass);
    }

    public function getComponentClass()
    {
        return $this->_componentClass;
    }
}
