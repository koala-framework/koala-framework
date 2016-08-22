<?php
class Kwc_Shop_ProductList_Trl_Component extends Kwc_Directories_List_Trl_Component
{
    public static function getSettings($masterComponentClass = null)
    {
        $ret = parent::getSettings($masterComponentClass);
        $ret['extConfig'] = 'Kwf_Component_Abstract_ExtConfig_None';
        return $ret;
    }

    public function getSelect($overrideValues = array())
    {
        $ret = parent::getSelect($overrideValues);
        $cmp = $this->getData()->chained->getComponent()->getRow()->component;
        if ($cmp) {
            $ret->whereEquals('component', $cmp);
        }
        return $ret;
    }
}
