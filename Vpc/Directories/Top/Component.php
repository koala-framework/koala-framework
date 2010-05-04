<?php
abstract class Vpc_Directories_Top_Component extends Vpc_Directories_List_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['limit'] = 5;
        return $ret;
    }

    public function getSelect($overrideValues = array())
    {
        $select = parent::getSelect($overrideValues);
        if (!$select) return null;
        if ($this->_getSetting('limit')) $select->limit($this->_getSetting('limit'));
        return $select;
    }
}
