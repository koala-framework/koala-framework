<?php
class Vpc_Directories_Menu_Component extends Vpc_Menu_Abstract
{
    public function getTemplateVars()
    {
        $ret = parent::getTemplateVars();
        $ret['categories'] = array();
        $itemDirectory = $this->_getItemDirectory();
        
        if ($itemDirectory) {
            $classes = Vpc_Abstract::getChildComponentClasses($itemDirectory->componentClass);
            foreach ($classes as $c) {
                if (Vpc_Abstract::hasSetting($c, 'categoryChildId')) {
                    $name = Vpc_Abstract::getSetting($c, 'categoryName');
                    $parent = Vps_Component_Data_Root::getInstance()
                        ->getComponentById($itemDirectory->componentId.'_'.Vpc_Abstract::getSetting($c, 'categoryChildId'));
                    $ret['categories'][$name] = $this->_getMenuData($parent);
                }
            }
        }
        return $ret;
    }

    protected function _getItemDirectory()
    {
        $data = $this->getData();
        while ($data = $data->parent) {
            if (is_instance_of($data->componentClass, 'Vpc_Directories_List_Component')) {
                return $data->getComponent()->getItemDirectory();
            }
        }
        return null;
    }

}
