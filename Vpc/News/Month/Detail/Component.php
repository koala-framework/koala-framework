<?php
class Vpc_News_Month_Detail_Component extends Vpc_Directories_List_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['generators']['child']['component']['view'] = 'Vpc_News_List_View_Component';
        return $ret;
    }

    public function getSelect()
    {
        $select = parent::getSelect();
        $monthDate = substr($this->getData()->row->publish_date, 0, 7);
        $select->where('publish_date >= ?', "$monthDate-01");
        $select->where('publish_date <= ?', "$monthDate-31");
        return $select;
    }

    protected function _getItemDirectory()
    {
        return $this->getData()->parent->parent;
    }
}
