<?php
class Vpc_Abstract_List_Trl_Generator extends Vpc_Chained_Trl_Generator
{
    protected function _createData($parentData, $row, $select)
    {
        $ret = parent::_createData($parentData, $row, $select);
        if ($select->getPart(Vps_Component_Select::IGNORE_VISIBLE) !== true) {
            $r = $this->_getRow($parentData->dbId.$this->getIdSeparator().$this->_getIdFromRow($row));
            if (!$r || !$r->visible) {
                $ret = null;
            }
        }
        return $ret;
    }

    protected function _formatConfig($parentData, $row)
    {
        $ret = parent::_formatConfig($parentData, $row);
        $id = $parentData->dbId.$this->getIdSeparator().$this->_getIdFromRow($row);
        $ret['row'] = $this->_getRow($id);
        if (!$ret['row']) {
            $m = Vpc_Abstract::createChildModel($this->_class);
            $ret['row'] = $m->createRow();
            $ret['row']->component_id = $id;
        }
        return $ret;
    }
}
