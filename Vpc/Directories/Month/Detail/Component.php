<?php
class Vpc_Directories_Month_Detail_Component extends Vpc_Directories_List_Component
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
        $dateColumn = Vpc_Abstract::getSetting($this->getData()->parent->componentClass, 'dateColumn');
        $select = $this->_getDateSelect($select, $dateColumn);
        return $select;
    }

    protected function _getDateSelect($select, $dateColumn)
    {
        $monthDate = substr($this->getData()->row->$dateColumn, 0, 7);
        $select->where($dateColumn.' >= ?', "$monthDate-01 00:00:00");
        $select->where($dateColumn.' <= ?', "$monthDate-31 23:59:59");
        $select->order($dateColumn, 'DESC');
        return $select;
    }

    protected function _getItemDirectory()
    {
        return $this->getData()->parent->parent;
    }
}
