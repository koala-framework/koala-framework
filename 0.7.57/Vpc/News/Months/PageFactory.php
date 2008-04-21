<?php
class Vpc_News_Months_PageFactory extends Vpc_Abstract_StaticPageFactory
{
    protected $_pages = array();
    protected $_additionalFactories = array();

    protected function _init()
    {
        parent::_init();
        $childComponentClasses = Vpc_Abstract::getSetting(get_class($this->_component), 'childComponentClasses');
        foreach ($this->getDateVars() as $month) {
            $this->_pages[] = array(
                'id'         => $month['year'].'-'.$month['month'],
                'name'       => $month['monthName'].' '.$month['year'],
                'filename'   => $month['year'].'_'.$month['month'],
                'showInMenu' => false,
                'componentClass' => $childComponentClasses['details']
            );
        }
    }

    public function getDateVars()
    {
        $ret = array();
        $monthLimit = $this->_getComponentSetting('monthLimit');
        $sort = $this->_getComponentSetting('sort');

        $date = new Zend_Date();
        $toDate = $date->get(Zend_Date::YEAR).'-'.$date->get(Zend_Date::MONTH).'-'.$date->get(Zend_Date::DAY);
        $date->subMonth($monthLimit - 1);
        $fromDate = $date->get(Zend_Date::YEAR).'-'.$date->get(Zend_Date::MONTH).'-01';

        $table = $this->_component->getTable();
        $sqlTableName = $table->info();
        $sqlTableName = $sqlTableName['name'];
        $where = "publish_date >= '$fromDate' AND publish_date <= '$toDate' AND visible = 1";

        $select = $table->getAdapter()->select();
        $select->from($sqlTableName, 'LEFT(publish_date, 7) availableDate');
        $select->where($where);
        $select->group('LEFT(publish_date, 7)');
        $select->order('LEFT(publish_date, 7) '.$sort);

        $rowSet = $table->getAdapter()->fetchAll($select);

        $date = new Zend_Date();
        foreach ($rowSet as $row) {
            $date->set(strtotime($row['availableDate'].'-01'));
            $ret[] = array(
                'year' => $date->get(Zend_Date::YEAR),
                'month' => $date->get(Zend_Date::MONTH),
                'monthName' => $date->get(Zend_Date::MONTH_NAME)
            );
        }

        return $ret;
    }

}
