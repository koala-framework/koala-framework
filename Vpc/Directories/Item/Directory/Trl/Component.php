<?php
class Vpc_Directories_Item_Directory_Trl_Component extends Vpc_Directories_List_Trl_Component
{
    public static function getSettings($masterComponentClass)
    {
        $ret = parent::getSettings($masterComponentClass);
        $ret['generators']['detail']['class'] = 'Vpc_Directories_Item_Directory_Trl_Generator';
        $ret['extConfig'] = 'Vpc_Directories_Item_Directory_Trl_ExtConfigEditButtons';
        return $ret;
    }

    public static function getCacheModelsForView($componentClass)
    {
        // TODO Cache Test gibts keinen dafür
        $ret = array();
        $generator = Vps_Component_Generator_Abstract::getInstance(
            Vpc_Abstract::getComponentClassByParentClass($componentClass), 'detail'
        );
        $ret[] = $generator->getModel();

        if (Vpc_Abstract::hasSetting($componentClass, 'childModel')) {
            $ret[] = Vps_Model_Abstract::getInstance(
                Vpc_Abstract::getSetting($componentClass, 'childModel')
            );
        }
        return $ret;
    }
}
