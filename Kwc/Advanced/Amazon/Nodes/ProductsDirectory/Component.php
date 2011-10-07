<?php
class Vpc_Advanced_Amazon_Nodes_ProductsDirectory_Component extends Vpc_Directories_ItemPage_Directory_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['generators']['detail']['class'] = 'Vpc_Advanced_Amazon_Nodes_ProductsDirectory_Generator';
        $ret['generators']['detail']['component'] = 'Vpc_Advanced_Amazon_Nodes_ProductsDirectory_Detail_Component';
        $ret['generators']['detail']['model'] = 'Vps_Util_Model_Amazon_Products';
        return $ret;
    }

    public function getTemplateVars()
    {
        throw new Vps_Exception_NotFound();
    }
}
