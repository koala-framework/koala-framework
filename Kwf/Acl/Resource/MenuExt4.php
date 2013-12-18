<?php
class Kwf_Acl_Resource_MenuExt4 extends Kwf_Acl_Resource_Abstract
{
    private $_extController;

    public function __construct($resourceId, $menuConfig, $extController)
    {
        $this->_extController = $extController;
        parent::__construct($resourceId, $menuConfig);
    }

    public function getExtController()
    {
        return $this->_extController;
    }
}
