<?php
class Kwc_List_Switch_Trl_ItemPageGenerator extends Kwc_Chained_Trl_Generator
{
    protected function _createData($parentData, $row, $select)
    {
        $ret = parent::_createData($parentData, $row, $select);
        if ($select->getPart(Kwf_Component_Select::IGNORE_VISIBLE) !== true) {
            $r = $this->_getRow($parentData->dbId.'-'.$this->_getIdFromRow($row));
            if (!$r || !$r->visible) {
                $ret = null;
            }
        }
        return $ret;
    }
}
