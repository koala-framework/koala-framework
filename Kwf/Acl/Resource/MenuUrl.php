<?php
class Kwf_Acl_Resource_MenuUrl extends Kwf_Acl_Resource_Abstract
{
    protected $_menuUrl;

    public function __construct($resourceId, $menuConfig = null, $menuUrl = null)
    {
        $this->_menuUrl = $menuUrl;
        parent::__construct($resourceId, $menuConfig);
    }

    public function setMenuUrl($menuUrl)
    {
        $this->_menuUrl = $menuUrl;
    }

    public function getMenuUrl()
    {
        if (!$this->_menuUrl) {
            $id = $this->getResourceId();
            $id = explode('_', $id);
            if ($id[0] == 'kwf' || $id[0] == 'vkwf') {
                return Kwf_Controller_Front::getInstance()->getRouter()->assemble(array(
                    'module' => $id[1],
                    'controller' => $id[2],
                ), $id[0].'_'.$id[1], true);
            } else {
                return Kwf_Controller_Front::getInstance()->getRouter()->assemble(array(
                    'module' => $id[0],
                    'controller' => $id[1],
                ), 'admin', true);
            }
        }
        return $this->_menuUrl;
    }
}
