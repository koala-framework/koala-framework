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
                    try {
                        $menu['menuConfig']['icon'] = Vps_Assets_Loader::getAssetPath(substr($menu['menuConfig']['icon'], 8), $assetPaths);
                    } catch (Vps_Assets_NotFoundException $e) {
                        $menu['menuConfig']['icon'] = '/assets/silkicons/'.$menu['menuConfig']['icon'];
                    }
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

        $this->view->menus = $menus;
        $this->view->showLogout = $showLogout;
        $this->view->authData = $this->_getAuthData();
    }

    public function jsonClearAssetsCacheAction()
    {
        $config = Zend_Registry::get('config');
        if ($config->debug->errormail) { //todo, besserer debug-modus
            throw new Vps_Exception("Debug is not enabled");
        }
        foreach (new DirectoryIterator('application/cache/assets') as $file) {
            if ($file->isFile()) {
                unlink($file->getPathname());
            }
        }
    }
}
