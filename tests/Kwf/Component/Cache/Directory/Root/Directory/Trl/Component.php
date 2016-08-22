<?php
class Kwf_Component_Cache_Directory_Root_Directory_Trl_Component extends Kwc_Directories_Item_Directory_Trl_Component
{
    public static function getSettings($masterComponentClass = null)
    {
        $ret = parent::getSettings($masterComponentClass);
        $ret['childModel'] = 'Kwf_Component_Cache_Directory_Root_Directory_Trl_Model';
        $ret['flags']['chainedType'] = 'Trl';
        $ret['flags']['hasAllChainedByMaster'] = true;
        return $ret;
    }

    public static function getAllChainedByMasterFromChainedStart($componentClass, $master, $chainedType, $parentDataSelect)
    {
        if (Kwc_Abstract::getFlag($componentClass, 'chainedType') != $chainedType) return array();
        $ret = array();
        foreach (Kwf_Component_Data_Root::getInstance()->getComponentsByClass($componentClass, $parentDataSelect) as $chainedStart) {
            $i = Kwc_Chained_Abstract_Component::getChainedByMaster($master, $chainedStart, $chainedType, $parentDataSelect);
            if ($i) $ret[] = $i;
        }
        return $ret;
    }
}
