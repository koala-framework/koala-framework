<?php
abstract class Kwc_Directories_Top_Component extends Kwc_Directories_List_Component
{
    public static function getSettings($param = null)
    {
        $ret = parent::getSettings($param);
        $ret['limit'] = 5;
        $ret['extConfig'] = 'Kwf_Component_Abstract_ExtConfig_None';
        return $ret;
    }

    public function getSelect()
    {
        $select = parent::getSelect();
        if (!$select) return null;
        if ($this->_getSetting('limit')) $select->limit($this->_getSetting('limit'));
        return $select;
    }
}
