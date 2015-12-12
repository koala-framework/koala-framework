<?php
/**
 * @deprectated
 */
class Kwc_Directories_List_View_ComponentNonPartial extends Kwc_Directories_List_View_Component
{
    public function getTemplateVars(Kwf_Component_Renderer_Abstract $renderer = null)
    {
        $ret = parent::getTemplateVars($renderer);
        $ret['items'] = $this->_getItems();
        return $ret;
    }
}
