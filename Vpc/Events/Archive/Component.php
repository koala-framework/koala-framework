<?php
class Vpc_Events_Archive_Component extends Vpc_Directories_List_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['useDirectorySelect'] = false;
        return $ret;
    }
    public function getSelect()
    {
        $select = parent::getSelect();
        if (!$select) return null;
        $select->where('IF(ISNULL(end_date), start_date, end_date) < NOW()');
        $select->order('start_date', 'DESC');
        return $select;
    }

    protected function _getItemDirectory()
    {
        return $this->getData()->parent;
    }
}
