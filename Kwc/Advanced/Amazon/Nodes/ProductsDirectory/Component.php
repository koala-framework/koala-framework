<?php
class Kwc_Advanced_Amazon_Nodes_ProductsDirectory_Component extends Kwc_Directories_ItemPage_Directory_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['generators']['detail']['class'] = 'Kwc_Advanced_Amazon_Nodes_ProductsDirectory_Generator';
        $ret['generators']['detail']['component'] = 'Kwc_Advanced_Amazon_Nodes_ProductsDirectory_Detail_Component';
        $ret['generators']['detail']['model'] = 'Kwf_Util_Model_Amazon_Products';
        return $ret;
    }

    public function getTemplateVars(Kwf_Component_Renderer_Abstract $renderer = null)
    {
        throw new Kwf_Exception_NotFound();
    }
}
