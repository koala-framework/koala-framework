<?php
class Vpc_News_Menu_Component extends Vpc_Menu_Abstract
{
    public function getTemplateVars()
    {
        $ret = parent::getTemplateVars();
        $ret['categories'] = array();
        $news = $this->getTreeCacheRow()->findParentComponent()
                            ->getComponent()->getNewsComponent();
        $classes = Vpc_Abstract::getSetting($news, 'childComponentClasses');
        foreach ($classes as $id=>$c) {
            if (Vpc_Abstract::hasSetting($c, 'categoryChildId')) {
                $name = Vpc_Abstract::getSetting($c, 'categoryName');
                $parentId = $news->getTreeCacheRow()->component_id.'_'.
                            Vpc_Abstract::getSetting($c, 'categoryChildId');
                $ret['categories'][$name] = $this->_getMenuData($parentId);
            }
        }
        return $ret;
    }
}
