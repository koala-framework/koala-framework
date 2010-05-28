<?php
class Vpc_Directories_Year_Detail_Component extends Vpc_Directories_Month_Detail_Component
{
    protected function _getDateSelect($select, $dateColumn)
    {
        $monthDate = substr($this->getData()->row->$dateColumn, 0, 4);
        $select->where($dateColumn.' >= ?', "$monthDate-01-01 00:00:00");
        $select->where($dateColumn.' <= ?', "$monthDate-12-31 23:59:59");
        $select->order($dateColumn, 'DESC');
        return $select;
    }
}
