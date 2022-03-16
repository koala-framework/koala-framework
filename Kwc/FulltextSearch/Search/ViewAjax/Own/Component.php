<?php
class Kwc_FulltextSearch_Search_ViewAjax_Own_Component extends Kwc_FulltextSearch_Search_ViewAjax_AbstractComponent
{
    /**
     * @param Kwf_Component_Select $ret
     * @param Kwf_Model_Row_Data_Abstract $searchRow
     * @return Kwf_Component_Select
     * @throws Kwf_Exception
     */
    protected function _getSearchSelect($ret, $searchRow)
    {
        /** @var Kwf_Component_Select $select */
        $select = parent::_getSearchSelect($ret, $searchRow);

        $select->whereNotEquals('componentId', 'external_url-*');

        return $select;
    }
}
