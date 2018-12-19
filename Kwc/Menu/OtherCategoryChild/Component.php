<?php
class Kwc_Menu_OtherCategoryChild_Component extends Kwc_Basic_ParentContent_Component
{
    public static function getSettings($menuComponentClass = null)
    {
        $ret = parent::getSettings($menuComponentClass);
        $ret['viewCache'] = Kwc_Abstract::getSetting($menuComponentClass, 'viewCache');
        $ret['menuComponentClass'] = $menuComponentClass;
        return $ret;
    }

    protected function _getParentContentData()
    {
        $category = Kwc_Abstract::getSetting($this->_getSetting('menuComponentClass'), 'level');

        // get first parent with boxes as menu can also be below composite
        $firstParentWithMenuBoxes = $this->getData()->getPage();
        if (!$firstParentWithMenuBoxes) {
            $firstParentWithMenuBoxes = $this->getData()->getSubroot();
        }

        // get path from box-component to actual menu-component deeper inside the box-component
        $menuComponentIdPath = array();
        $menuComponentIdPath[] = $this->getData()->id;
        $ownBoxStartCmp = $this->getData();
        while ($ownBoxStartCmp->parent != $firstParentWithMenuBoxes
            && !Kwc_Abstract::getFlag($ownBoxStartCmp->parent->componentClass, 'menuCategory')
        ) {
            $ownBoxStartCmp = $ownBoxStartCmp->parent;
            $menuComponentIdPath[] = $ownBoxStartCmp->id;
        }
        $menuComponentIdPath = array_reverse($menuComponentIdPath);

        // get default menu-component
        $menu = $firstParentWithMenuBoxes->getChildComponent('-'.$category);
        foreach ($menuComponentIdPath as $componentId) {
            $menu = $menu->getChildComponent('-'.$componentId);
        }
        if (!is_instance_of($menu->componentClass, 'Kwc_Menu_Abstract_Component')) {
            throw new Kwf_Exception("got invalid menu component");
        }
        return $menu;
    }
}
