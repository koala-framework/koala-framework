<?php
class Kwc_Shop_Products_Directory_Trl_AddToCartGenerator extends Kwc_Chained_Trl_Generator
{
    protected function _createData($parentData, $row, $select)
    {
        $ret = parent::_createData($parentData, $row, $select);
        if ($select->getPart(Kwf_Component_Select::IGNORE_VISIBLE) !== true) {
            $r = $this->_getRow($parentData->dbId.'_'.$this->_getIdFromRow($row)); //_ separator to get visiblity of product
            if (!$r || !$r->visible) {
                $ret = null;
            }
        }
        return $ret;
    }
}
