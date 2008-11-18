<?php
class Vps_Acl_Resource_Component_MenuUrl extends Vps_Acl_Resource_MenuUrl
    implements Vps_Acl_Resource_Component_Interface
{
    protected $_component;

    public function __construct(Vps_Component_Data $component, $menuConfig = null, $menuUrl = null)
    {
        $this->_component = $component;
        parent::__construct('vpc_'.$component->dbId, $menuConfig, $menuUrl);
    }

    public function getComponent()
    {
        return $this->_component;
    }
}
