<?php
class Vps_Controller_Action_User_Menu extends Vps_Controller_Action
{
    protected function _processResources($resources)
    {
        $acl = $this->_getAcl();

        $menus = array();
        foreach ($resources as $resource) {
            if ($acl->isAllowed($this->_getUserRole(), $resource)) {
                $menu = array();
                if ($resource instanceof Vps_Acl_Resource_MenuDropdown) {
                    $menu['type'] = 'dropdown';
                    $menu['text'] = $resource->getMenuText();
                    $menu['children'] = $this->_processResources($acl->getResources($resource));
                } else if ($resource instanceof Vps_Acl_Resource_MenuEvent) {
                    $menu['type'] = 'event';
                    $menu['text'] = $resource->getMenuText();
                    $menu['config'] = $resource->getMenuConfig();
                } else if ($resource instanceof Vps_Acl_Resource_MenuUrl) {
                    $menu['type'] = 'url';
                    $menu['text'] = $resource->getMenuText();
                    $menu['url'] = $resource->getMenuUrl();
                } else if ($resource instanceof Vps_Acl_Resource_MenuCommandDialog) {
                    $menu['type'] = 'commandDialog';
                    $menu['text'] = $resource->getMenuText();
                    $menu['commandClass'] = $resource->getMenuCommandClass();
                    $menu['config'] = $resource->getMenuConfig();
                } else if ($resource instanceof Vps_Acl_Resource_MenuCommand) {
                    $menu['type'] = 'command';
                    $menu['text'] = $resource->getMenuText();
                    $menu['commandClass'] = $resource->getMenuCommandClass();
                    $menu['config'] = $resource->getMenuConfig();
                } else if ($resource instanceof Zend_Acl_Resource) {
                    continue; //nicht im menü anzeigen
                } else {
                    throw new Vps_Exception("Unknown resource-type '".get_class($resource)."'");
                }
                $menus[] = $menu;
            }
        }
        return $menus;
    }
    public function jsonDataAction()
    {
        $showLogout = true;
        $resources = $this->_getAcl()->getResources();
        $menus = $this->_processResources($resources);

        if (empty($menus) && $this->_getUserRole() == 'guest') {
            $menu = array();
            $menu['type'] = 'commandDialog';
            $menu['text'] = 'Login';
            $menu['commandClass'] = 'Vps.User.Login.Dialog';
            $menus[] = $menu;
            $showLogout = false;
        }

        $this->view->menus = $menus;
        $this->view->showLogout = $showLogout;
    }
}
