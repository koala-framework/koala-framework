<?php
class Kwf_Filter_Row_FilenameParents extends Kwf_Filter_Row_Abstract
{
    protected $_separator = '/';
    protected $_filter = 'Ascii';
    protected $_parentRule;

    //parentRule wird nur mit Kwf_Model verwendet, nicht mit Zend_Db_Table
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
            $ret = Kwf_Filter::filterStatic($row->__toString(), $this->_filter)
                . $ret;
            $row = $row->getParentRow($this->_parentRule);
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
