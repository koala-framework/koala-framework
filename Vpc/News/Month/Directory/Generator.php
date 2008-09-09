<?php
class Vpc_News_Month_Directory_Generator extends Vps_Component_Generator_Page_Table
{
    protected $_uniqueFilename = true;
    public function getChildData($parentData, $select = array())
    {
        $ret = array();
        $select = $this->_formatSelect($parentData, $select);
        $rows = array();
        if ($select) {
            $rows = $this->_getModel()->fetchAll($select);
        }
        foreach ($rows as $row) {
            $ret[] = $this->_createData($parentData, $row, $select);
        }
        return $ret;
    }

    protected function _formatSelectFilename(Vps_Component_Select $select)
    {
        if ($select->hasPart(Vps_Component_Select::WHERE_FILENAME)) {
            $filename = $select->getPart(Vps_Component_Select::WHERE_FILENAME);
            if (!preg_match('#^([0-9]{4})_([0-9]{2})$#', $filename, $m)) return null;
            $select->where("YEAR(publish_date) = ?", $m[1]);
            $select->where("MONTH(publish_date) = ?", $m[2]);
        }
        return $select;
    }

    protected function _formatSelectId(Vps_Component_Select $select)
    {
        if ($select->hasPart(Vps_Model_Select::WHERE_ID)) {
            $id = $select->getPart(Vps_Model_Select::WHERE_ID);
            if (!preg_match('#^_([0-9]{4})([0-9]{2})$#', $id, $m)) return null;
            $select->where("YEAR(publish_date) = ?", $m[1]);
            $select->where("MONTH(publish_date) = ?", $m[2]);
            $select->unsetPart(Vps_Model_Select::WHERE_ID);
        }
        return $select;
    }

    protected function _formatSelect($parentData, $select)
    {
        $ret = parent::_formatSelect($parentData, $select);
        if (!$ret) return $ret;
        $ret->group(array('YEAR(publish_date)', 'MONTH(publish_date)'));
        $ret->order('publish_date', 'DESC');
        $ret->whereEquals('component_id', $parentData->parent->dbId);
        return $ret;
    }

    protected function _getNameFromRow($row)
    {
        $date = new Zend_Date($row->publish_date);
        return $date->get(Zend_Date::MONTH_NAME).' '.$date->get(Zend_Date::YEAR);
    }

    protected function _getFilenameFromRow($row)
    {
        return date('Y_m', strtotime($row->publish_date));
    }
    protected function _getIdFromRow($row)
    {
        return date('Ym', strtotime($row->publish_date));
    }
}
