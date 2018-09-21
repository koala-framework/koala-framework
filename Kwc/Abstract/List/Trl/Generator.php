<?php
class Kwc_Abstract_List_Trl_Generator extends Kwc_Chained_Trl_Generator
{
    protected function _formatConfig($parentData, $row)
    {
        $ret = parent::_formatConfig($parentData, $row);
        $r = $this->_getRow($parentData->dbId.$this->getIdSeparator().$this->_getIdFromRow($row));
        $ret['invisible'] = !$r || !$r->visible;
        return $ret;
    }

    protected function _createData($parentData, $row, $select)
    {
        $ret = parent::_createData($parentData, $row, $select);
        if ($select->getPart(Kwf_Component_Select::IGNORE_VISIBLE) !== true) {
            if (isset($ret->invisible) && $ret->invisible) {
                $ret = null;
            }
        }
        return $ret;
    }

    public function getTrlRowByData(Kwf_Component_Data $data)
    {
        $ret = $this->_getRow($data->dbId);
        if (!$ret) {
            $m = Kwc_Abstract::createChildModel($this->_class);
            $ret = $m->createRow();
            $ret->component_id = $data->dbId;
        }
        return $ret;
    }
}
