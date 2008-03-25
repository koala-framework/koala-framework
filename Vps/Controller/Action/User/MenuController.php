<?php
class Vps_Controller_Action_User_MenuController extends Vps_Controller_Action
{
    protected function _processResources($resources)
    {
        $acl = $this->_getAcl();

        $assetPaths = Zend_Registry::get('config')->path;

        $menus = array();
        foreach ($resources as $resource) {
            if ($acl->isAllowed($this->_getUserRole(), $resource, 'view')) {
                if (!$resource instanceof Vps_Acl_Resource_Abstract) {
                    //nur Vps-Resourcen im Menü anzeigen
                    continue;
                }
                $menu = array();
                $menu['menuConfig'] = $resource->getMenuConfig();
                if (is_string($menu['menuConfig'])) {
                    $menu['menuConfig'] = array('text' => $menu['menuConfig']);
                }

                if (isset($menu['menuConfig']['icon'])) {
                    if (is_string($menu['menuConfig']['icon'])) {
                        $menu['menuConfig']['icon'] = new Vps_Asset($menu['menuConfig']['icon']);
                    }
                    $menu['menuConfig']['icon'] = $menu['menuConfig']['icon']->__toString();
                }

                if ($resource instanceof Vps_Acl_Resource_MenuDropdown) {
                    $menu['type'] = 'dropdown';
                    $menu['children'] = $this->_processResources($acl->getResources($resource));
                } else if ($resource instanceof Vps_Acl_Resource_MenuEvent) {
                    $menu['type'] = 'event';
                    $menu['eventConfig'] = $resource->getMenuEventConfig();
                } else if ($resource instanceof Vps_Acl_Resource_MenuUrl) {
                    $menu['type'] = 'url';
                    $menu['url'] = $resource->getMenuUrl();
                } else if ($resource instanceof Vps_Acl_Resource_MenuCommandDialog) {
                    $menu['type'] = 'commandDialog';
                    $menu['commandClass'] = $resource->getMenuCommandClass();
                    $menu['commandConfig'] = $resource->getMenuCommandConfig();
                } else if ($resource instanceof Vps_Acl_Resource_MenuCommand) {
                    $menu['type'] = 'command';
                    $menu['commandClass'] = $resource->getMenuCommandClass();
                    $menu['commandConfig'] = $resource->getMenuCommandConfig();
                } else if ($resource instanceof Vps_Acl_Resource_MenuSeparator) {
                    $menu['type'] = 'separator';
                } else {
                    $menu = $menu['menuConfig'];
                }
                $menus[] = $menu;
            }
        }
        return $menus;
    }
    public function jsonDataAction()
    {
        $showLogout = true;
        $acl = $this->_getAcl();
        $resources = $acl->getResources();
        $menus = $this->_processResources($resources);

        if (empty($menus) && $this->_getUserRole() == 'guest') {
            $menu = array();
            $menu['type'] = 'commandDialog';
            $menu['menuConfig']['text'] = 'Login';
            $menu['commandClass'] = 'Vps.User.Login.Dialog';
            $menus[] = $menu;
            $showLogout = false;
        }

        foreach ($acl->getAllResources() as $resource) {
            if ($resource instanceof Vps_Acl_Resource_UserSelf
                && $acl->isAllowed($this->_getUserRole(), $resource, 'view')) {

                $this->view->userSelfControllerUrl = $resource->getControllerUrl();
                break;
            }
        }

        $authData = $this->_getAuthData();

        $this->view->menus = $menus;
        $this->view->showLogout = $showLogout;
        $this->view->userId = $authData ? $authData->id : 0;
        $this->view->fullname = $authData ? $authData->__toString() : '';

        $role = Zend_Registry::get('userModel')->getAuthedChangedUserRole();
        $this->view->changeUser = $acl->isAllowed($role, 'vps_user_changeuser', 'view');
    }

    public function jsonClearAssetsCacheAction()
    {
        $config = Zend_Registry::get('config');
        if ($config->debug->errormail) { //todo, besserer debug-modus
            throw new Vps_Exception(trlVps("Debug is not enabled"));
        }
        foreach (new DirectoryIterator('application/cache/assets') as $file) {
            if ($file->isFile()) {
                unlink($file->getPathname());
            }
        }
    }
}
