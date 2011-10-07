<?php
class Vps_Acl_Resource_ComponentClass_MenuUrl extends Vps_Acl_Resource_MenuUrl
    implements Vps_Acl_Resource_ComponentClass_Interface
{
    protected $_componentClass;

    public function __construct($resourceId, $menuConfig = null, $menuUrl = null, $componentClass = null)
    {
        if (!$componentClass) $componentClass = $resourceId;
        $this->_componentClass = $componentClass;
        parent::__construct('vpc_'.$resourceId, $menuConfig, $menuUrl);
    }

    public function getComponentClass()
    {
        return $this->_componentClass;
    }
}
