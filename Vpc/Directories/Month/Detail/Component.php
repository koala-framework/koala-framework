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
        $select->where(new Vps_Model_Select_Expr_HigherEqual($dateColumn, new Vps_Date("$monthDate-01")));
        $select->where(new Vps_Model_Select_Expr_LowerEqual($dateColumn, new Vps_Date("$monthDate-31")));
        $select->order($dateColumn, 'DESC');
        return $select;
    }

    protected function _getItemDirectory()
    {
        return $this->getData()->parent->parent;
    }
}
