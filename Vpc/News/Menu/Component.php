<?php
class Vpc_News_Menu_Component extends Vpc_Menu_Abstract
{
    public function getTemplateVars()
    {
        $ret = parent::getTemplateVars();
        $ret['categories'] = array();
        $news = $this->getData()->getParentComponent()
                            ->getComponent()->getNewsComponent();
        $classes = Vpc_Abstract::getChildComponentClasses($news, 'child');
        foreach ($classes as $id=>$c) {
            if (Vpc_Abstract::hasSetting($c, 'categoryChildId')) {
                $name = Vpc_Abstract::getSetting($c, 'categoryName');
                $parentId = $news->getData()->componentId.'_'.
                            Vpc_Abstract::getSetting($c, 'categoryChildId');
                $ret['categories'][$name] = $this->_getMenuData($parentId);
            }
        }
        return $ret;
    }
}
