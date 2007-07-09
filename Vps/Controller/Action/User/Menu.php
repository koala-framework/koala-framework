<?php
class Vps_Controller_Action_User_Menu extends Vps_Controller_Action
{
    public function jsonDataAction()
    {
        $showLogout = true;
        $userRole = $this->_getUserRole();
        $menus = array();
        $acl = $this->_getAcl();
        $resources = $acl->getResources();
        foreach ($resources as $resource) {
            if ($resource instanceof Vps_Acl_Resource && $acl->isAllowed($userRole, $resource) && $resource->getMenuText()) {
                $childResources = $acl->getResources($resource);
                $menu = array();
                $menu['text'] = $resource->getMenuText();
                $menu['url'] = $resource->getMenuUrl();
                $menu['asEvent'] = $resource->asEvent();
                $menu['children'] = array();
                foreach ($childResources as $cr) {
                    if ($cr instanceof Vps_Acl_Resource && $acl->isAllowed($userRole, $cr)) {
                        $m = array();
                        $m['text'] = $cr->getMenuText();
                        $m['url'] = $cr->getMenuUrl();
                        $m['asEvent'] = $cr->asEvent();
                        $menu['children'][] = $m;
                    }
                }
                $menus[] = $menu;
            }
        }
        
        if (empty($menus) && $userRole == 'guest') {
            $menu = array();
            $menu['text'] = 'Login';
            $menu['url'] = '/login/';
            $menu['children'] = array();
            $menus[] = $menu;
            $showLogout = false;
        }
        
        
        $this->view->menus = $menus;
        $this->view->showLogout = $showLogout;
    }
}
