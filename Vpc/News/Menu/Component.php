<?php
class Vpc_News_Menu_Component extends Vpc_Menu_Abstract
{
    public function getTemplateVars()
    {
        $ret = parent::getTemplateVars();
        $ret['categories'] = array();
        $news = $this->getData()->parent->getComponent()->getNewsComponent();
        $classes = Vpc_Abstract::getChildComponentClasses($news->componentClass);
        foreach ($classes as $id=>$c) {
            if (Vpc_Abstract::hasSetting($c, 'categoryChildId')) {
                $name = Vpc_Abstract::getSetting($c, 'categoryName');
                $parent = Vps_Component_Data_Root::getInstance()
                    ->getComponentById($news->componentId.'_'.Vpc_Abstract::getSetting($c, 'categoryChildId'));
                $ret['categories'][$name] = $this->_getMenuData($parent);
            }
        }
        return $ret;
    }

}
