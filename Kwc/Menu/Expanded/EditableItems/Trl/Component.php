<?php
class Kwc_Menu_Expanded_EditableItems_Trl_Component extends Kwc_Chained_Trl_Component
{
    public static function getSettings($masterComponentClass = null)
    {
        $ret = parent::getSettings($masterComponentClass);
        $ret['extConfig'] = 'Kwc_Menu_Expanded_EditableItems_ExtConfig';
        return $ret;
    }

    public function attachEditableToMenuData(&$menuData)
    {
        foreach ($menuData as $k=>&$m) {
            if (isset($m['editable']) && $m['editable']) {
                $m['editable'] = Kwc_Chained_Trl_Component::getChainedByMaster($m['editable'], $this->getData());
            }
        }
    }
}
