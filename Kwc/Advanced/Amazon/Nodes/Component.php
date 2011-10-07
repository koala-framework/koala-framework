<?php
class Vpc_Advanced_Amazon_Nodes_Component extends Vpc_Directories_ItemPage_Directory_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['componentName'] = trlVps('Amazon.Nodes');
        $ret['ownModel'] = 'Vpc_Advanced_Amazon_Nodes_FieldModel';

        $ret['generators']['detail']['component'] = 'Vpc_Advanced_Amazon_Nodes_Detail_Component';
        $ret['generators']['detail']['model'] = 'Vpc_Advanced_Amazon_Nodes_NodesModel';
        $ret['generators']['child']['component']['view'] = 'Vpc_Advanced_Amazon_Nodes_View_Component';

        $ret['generators']['products'] = array(
            'class' => 'Vps_Component_Generator_Page_Static',
            'name' => trlVps('Products'),
            'component' => 'Vpc_Advanced_Amazon_Nodes_ProductsDirectory_Component'
        );

        return $ret;
    }
}
