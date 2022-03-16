<?php
class Kwc_FulltextSearch_Search_ViewAjax_External_Component extends Kwc_FulltextSearch_Search_ViewAjax_AbstractComponent
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

        $select->where(new Kwf_Model_Select_Expr_Equal('componentId', 'external_url-*'));

        return $select;
    }

    /**
     * @param Kwf_Component_Renderer_Abstract $renderer
     * @return array
     */
    public function getTemplateVars(Kwf_Component_Renderer_Abstract $renderer)
    {
        $ret = parent::getTemplateVars($renderer);
        $itemDir = $this->getData()->parent->getComponent()->getItemDirectory();
        if (!is_string($itemDir)) {
            $ret['config']['directoryViewComponentId'] = $itemDir->getChildComponent('-viewExternal')->componentId;
            $ret['config']['directoryComponentId'] = $itemDir->componentId;
            $ret['config']['directoryComponentClass'] = $itemDir->componentClass;
        }

        return $ret;
    }
}
