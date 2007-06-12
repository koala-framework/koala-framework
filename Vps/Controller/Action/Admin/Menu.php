<?php
class Vps_Controller_Action_Admin_Menu extends Vps_Controller_Action
{
    public function ajaxDataAction()
    {
        $currentRole = $this->_getUserRole();
        $menus = array();
        $acl = $this->_getAcl();
        $resources = $acl->getResources();
        foreach ($resources as $resource) {
            if ($acl->isAllowed($currentRole, $resource) && $resource->getMenuText()) {
                $childResources = $acl->getResources($resource);
                $menu = array();
                $menu['text'] = $resource->getMenuText();
                $menu['url'] = $resource->getMenuUrl();
                $menu['children'] = array();
                foreach ($childResources as $cr) {
                    if ($cr instanceof Vps_Acl_Resource && $acl->isAllowed($currentRole, $cr)) {
                        $m = array();
                        $m['text'] = $cr->getMenuText();
                        $m['url'] = $cr->getMenuUrl();
                        $menu['children'][] = $m;
                    }
                }
                $menus[] = $menu;
            }
        }
        $this->_helper->json('menus', $menus);
    }
}
