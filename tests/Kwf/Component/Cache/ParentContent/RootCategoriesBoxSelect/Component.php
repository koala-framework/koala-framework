<?php
class Kwf_Component_Cache_ParentContent_RootCategoriesBoxSelect_Component extends Kwc_Root_Component
{
    public static function getSettings($param = null)
    {
        $ret = parent::getSettings($param);
        $ret['generators']['category']['component'] = 'Kwf_Component_Cache_ParentContent_RootCategoriesBoxSelect_Category_Component';
        $ret['generators']['category']['model'] = new Kwf_Model_FnF(array(
            'data' => array(
                array('id'=>'top', 'name'=>'top'),
                array('id'=>'main', 'name'=>'main'),
            )
        ));
        unset($ret['generators']['title']);
        $ret['generators']['box'] = array(
            'class' => 'Kwf_Component_Generator_Box_StaticSelect',
            'component' => array(
                'parentContent' => 'Kwc_Basic_ParentContent_Component',
                'box' => 'Kwf_Component_Cache_ParentContent_RootCategoriesBoxSelect_Box_Component',
            ),
            'inherit' => true,
            'model' => 'Kwf_Component_Cache_ParentContent_RootCategoriesBoxSelect_BoxSelectModel',
        );
        return $ret;
    }
}
