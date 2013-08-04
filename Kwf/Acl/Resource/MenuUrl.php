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
            $id = str_replace('_', '/', $id);
            return Kwf_Config::getValue('kwc.urlPrefix').'/'.$id;
        }
        return $this->_menuUrl;
    }
}
