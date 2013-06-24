<?php
class Kwc_Newsletter_Subscribe_Resource extends Kwf_Acl_Resource_ComponentClass_MenuUrl
    implements Kwf_Acl_Resource_Component_Interface
{
    private $_component;

    public function __construct($resourceId, $menuConfig, $menuUrl, $componentClass, $component)
    {
        parent::__construct($resourceId, $menuConfig, $menuUrl, $componentClass);
        $this->_component = $component;
    }

    public function getComponent() {
        return $this->_component;
    }
}
