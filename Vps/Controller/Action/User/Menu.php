<?php

class Vps_Controller_Action_User_Menu extends Vps_Controller_Action
{
    protected function _processResources($resources)
    {
        $acl = $this->_getAcl();

        $config = Zend_Registry::get('config');
        $assetPaths = Vps_Assets_Dependencies::resolveAssetPaths($config->asset->toArray());

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
                //wenn ein kompletter assets-pfad angegeben wurde keine änderung
                if (isset($menu['menuConfig']['icon'])
                    && !Vps_Assets_Loader::getAssetPath(substr($menu['menuConfig']['icon'], 8), $assetPaths)) {
                    //sonst den standard-prefix dazugeben
                    $menu['menuConfig']['icon'] = '/assets/vps/images/silkicons/'.$menu['menuConfig']['icon'];
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
        $resources = $this->_getAcl()->getResources();
        $menus = $this->_processResources($resources);

        if (empty($menus) && $this->_getUserRole() == 'guest') {
            $menu = array();
            $menu['type'] = 'commandDialog';
            $menu['menuConfig']['text'] = 'Login';
            $menu['commandClass'] = 'Vps.User.Login.Dialog';
            $menus[] = $menu;
            $showLogout = false;
        }

        $this->view->menus = $menus;
        $this->view->showLogout = $showLogout;
        $this->view->userRole = $this->_getUserRole();
        $this->view->authData = $this->_getAuthData();
    }

    public function jsonClearAssetsCacheAction()
    {
        $config = Zend_Registry::get('config');
        if ($config->debug->errormail) { //todo, besserer debug-modus
            throw new Vps_Exception("Debug is not enabled");
        }
        foreach(new DirectoryIterator('application/cache/assets') as $file)
        {
            if ($file->isFile()) {
                unlink($file->getPathname());
            }
        }
    }
}
