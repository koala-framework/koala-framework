<?php
class Vps_Filter_Row_FilenameParents extends Vps_Filter_Row_Abstract
{
    protected $_separator = '/';
    protected $_filter = 'Ascii';

    public function filter($row)
    {
        $ret = '';
        do {
            if ($ret != '') {
                $ret = $this->_separator . $ret;
            }
            $ret = Vps_Filter::get($row->__toString(), $this->_filter)
                . $ret;
            $row = $row->findParentRow(get_class($row->getTable()));
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