<?php
class Vpc_News_Month_Detail_Component extends Vpc_Directories_List_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['generators']['child']['component']['view'] = 'Vpc_News_List_View_Component';
        $ret['useDirectorySelect'] = false;
        return $ret;
    }

    public function getSelect()
    {
        $select = parent::getSelect();
        $dateColumn = Vpc_Abstract::getSetting($this->parent->componentClass, 'dateColumn');
        $monthDate = substr($this->getData()->row->$dateColumn, 0, 7);
        $select->where($dateColumn.' >= ?', "$monthDate-01");
        $select->where($dateColumn.' <= ?', "$monthDate-31");
        $select->order($dateColumn, 'DESC');
        return $select;
    }

    protected function _getItemDirectory()
    {
        return $this->getData()->parent->parent;
    }
}
