<?php
class Kwc_Advanced_Amazon_Nodes_Component extends Kwc_Directories_ItemPage_Directory_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['componentName'] = trlKwfStatic('Amazon.Nodes');
        $ret['ownModel'] = 'Kwc_Advanced_Amazon_Nodes_FieldModel';

        $ret['generators']['detail']['component'] = 'Kwc_Advanced_Amazon_Nodes_Detail_Component';
        $ret['generators']['detail']['model'] = 'Kwc_Advanced_Amazon_Nodes_NodesModel';
        $ret['generators']['child']['component']['view'] = 'Kwc_Advanced_Amazon_Nodes_View_Component';

        $ret['generators']['products'] = array(
            'class' => 'Kwf_Component_Generator_Page_Static',
            'name' => trlKwfStatic('Products'),
            'component' => 'Kwc_Advanced_Amazon_Nodes_ProductsDirectory_Component'
        );

        return $ret;
    }
}
