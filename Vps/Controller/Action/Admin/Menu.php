<?php
class Vps_Controller_Action_Admin_Menu extends Vps_Controller_Action {
    
    public function ajaxDataAction()
    {
        $userNamespace = new Zend_Session_Namespace('User');
        $currentRole = $userNamespace->role;
        $menus = array();
        $acl = $this->_getAcl();
        $resources = $acl->getResources();
        foreach ($resources as $resource) {
            if ($acl->isAllowed($currentRole, $resource)) {
                $childResources = $acl->getResources($resource);
                if ($childResources) {
                    $menu = array();
                    $menu['children'] = array();
                    foreach ($childResources as $cr) {
                        if ($acl->isAllowed($currentRole, $cr)) {
                            $m = array();
                            $m['text'] = $cr->getMenuText();
                            $m['url'] = $cr->getMenuUrl();
                            $menu['children'][] = $m;
                        }
                    }
                    if ($menu['children']) {
                        $menu['text'] = $resource->getMenuText();
                        $menu['url'] = $resource->getMenuUrl();
                        $menus[] = $menu;
                    }
                }
            }
        }
        $this->getResponse()->appendJson('menus', $menus);
    }

}