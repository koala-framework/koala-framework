<?php
class Vps_Acl_Resource_ComponentClass_Dropdown extends Vps_Acl_Resource_MenuDropdown
    implements Vps_Acl_Resource_ComponentClass_Interface
{
    protected $_componentClass;

    public function __construct($resourceId, $menuConfig = null, $componentClass = null)
    {
        if (!$componentClass) $componentClass = $resourceId;
        $this->_componentClass = $componentClass;
        parent::__construct('vpc_'.$resourceId, $menuConfig);
    }

    public function getComponentClass()
    {
        return $this->_componentClass;
    }
}
