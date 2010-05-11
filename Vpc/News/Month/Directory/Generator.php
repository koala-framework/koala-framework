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
            $currentPd = array($parentData);
            if (!$parentData) {
                $currentPd = $this->_getParentDataByRow($row, $select);
            }
            foreach ($currentPd as $pd) {
                $ret[] = $this->_createData($pd, $row, $select);
            }
        }
        return $ret;
    }

    protected function _getParentDataByRow($row, $select)
    {
        $constraints = array();
        if ($select->hasPart(Vps_Component_Select::WHERE_SUBROOT)) {
            $constraints['subroot'] = $select->getPart(Vps_Component_Select::WHERE_SUBROOT);
        }
        if ($select->hasPart(Vps_Component_Select::IGNORE_VISIBLE)) {
            $constraints['ignoreVisible'] = $select->getPart(Vps_Component_Select::IGNORE_VISIBLE);
        }
        $news = Vps_Component_Data_Root::getInstance()
            ->getComponentsByDbId($row->component_id, $constraints);
        $ret = array();
        foreach ($news as $new) {
            $ret = array_merge($ret, $new->getChildComponents(array('componentClass'=>$this->_class)));
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
        if (!$parentData) {
            $page = $select->getPart(Vps_Component_Select::WHERE_CHILD_OF_SAME_PAGE);
            if (!$page) {
                return null;
            }
            $ret->where(new Vps_Model_Select_Expr_Like('component_id', $page->dbId.'-%'));
        } else {
            $ret->whereEquals('component_id', $parentData->parent->dbId);
        }
        return $ret;
    }

    protected function _getNameFromRow($row)
    {
        $date = new Vps_Date($row->publish_date);
        return $date->get(Vps_Date::MONTH_NAME).' '.$date->get(Vps_Date::YEAR);
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
