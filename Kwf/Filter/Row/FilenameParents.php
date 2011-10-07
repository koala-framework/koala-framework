<?php
class Vps_Filter_Row_FilenameParents extends Vps_Filter_Row_Abstract
{
    protected $_separator = '/';
    protected $_filter = 'Ascii';
    protected $_parentRule;

    //parentRule wird nur mit Vps_Model verwendet, nicht mit Zend_Db_Table
    public function __construct($parentRule = 'Parent')
    {
        $this->_parentRule = $parentRule;
    }

    public function filter($row)
    {
        $ret = '';
        do {
            if ($ret != '') {
                $ret = $this->_separator . $ret;
            }
            $ret = Vps_Filter::filterStatic($row->__toString(), $this->_filter)
                . $ret;
            if ($row instanceof Vps_Model_Row_Interface) {
                $row = $row->getParentRow($this->_parentRule);
            } else {
                $row = $row->findParentRow(get_class($row->getTable()));
            }
        } while ($row);

        return $ret;
    }

    public function setSeparator($sep)
    {
        $this->_separator = $sep;
    }

    public function setFilter($filter)
    {
        $this->_filter = $filter;
    }
}