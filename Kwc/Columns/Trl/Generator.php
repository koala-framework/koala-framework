<?php
class Kwc_Columns_Trl_Generator extends Kwc_Abstract_List_Trl_Generator
{
    //don't use visible
    protected function _createData($parentData, $row, $select)
    {
        return Kwc_Chained_Trl_Generator::_createData($parentData, $row, $select);
    }

    //don't use visible
    protected function _getChainedSelect($select)
    {
        return Kwc_Chained_Abstract_Generator::_getChainedSelect($select);
    }

}
