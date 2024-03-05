<?php
class Kwc_Paragraphs_Trl_Generator extends Kwc_Chained_Trl_Generator
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
}
