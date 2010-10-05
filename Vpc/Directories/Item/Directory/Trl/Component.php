<?php
class Vpc_Directories_Item_Directory_Trl_Component extends Vpc_Directories_List_Trl_Component
{
    public static function getSettings($masterComponentClass)
    {
        $ret = parent::getSettings($masterComponentClass);
        $ret['generators']['detail']['class'] = 'Vpc_Directories_Item_Directory_Trl_Generator';

        $ret['flags']['isItemDirectory'] = true; // für Cache löschen

        $ret['extConfig'] = 'Vpc_Directories_Item_Directory_Trl_ExtConfigEditButtons';
        return $ret;
    }

    public function getCacheMetaForView()
    {
        $ret = array();
        if (Vpc_Abstract::hasSetting($this->getData()->componentClass, 'childModel')) {
            $model = Vps_Model_Abstract::getInstance(
                Vpc_Abstract::getSetting($this->getData()->componentClass, 'childModel')
            );
            $ret[] = new Vpc_Directories_Item_Directory_Trl_CacheMeta($model);
        }
        return $ret;
    }
}
