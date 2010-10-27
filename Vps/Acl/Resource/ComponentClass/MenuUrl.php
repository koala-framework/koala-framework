<?php
class Vps_Acl_Resource_ComponentClass_MenuUrl extends Vps_Acl_Resource_MenuUrl
    implements Vps_Acl_Resource_ComponentClass_Interface
{
    protected $_componentClass;

    public function __construct($componentClass, $menuConfig = null, $menuUrl = null)
    {
        $this->_componentClass = $componentClass;
        parent::__construct('vpc_'.$componentClass, $menuConfig, $menuUrl);
    }

    public function getComponentClass()
    {
        return $this->_componentClass;
    }
}
