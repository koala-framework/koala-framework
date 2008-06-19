<?php
class Vpc_News_Month_Detail_Component extends Vpc_News_List_Abstract_Component
{
    protected function _selectNews()
    {
        $select = parent::_selectNews();
        $monthDate = $this->getData()->id;
        $select->where('publish_date >= ?', "$monthDate-01");
        $select->where('publish_date <= ?', "$monthDate-31");
        return $select;
    }

    public function getNewsComponent()
    {
        return $this->getData()
            ->getParentComponent() //month directory
            ->getNewsComponent();
    }
}
