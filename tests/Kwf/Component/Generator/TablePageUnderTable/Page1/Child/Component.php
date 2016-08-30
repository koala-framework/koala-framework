<?php
class Kwf_Component_Generator_TablePageUnderTable_Page1_Child_Component extends Kwc_Abstract
{
    public static function getSettings($param = null)
    {
        $ret = parent::getSettings($param);
        $ret['generators']['table'] = array(
            'class' => 'Kwf_Component_Generator_Page_Table',
            'model' => new Kwf_Model_FnF(array(
                'data' => array(
                    array('id'=>1, 'name' => 'asdf', 'component_id'=>'root_page1-1'),
                    array('id'=>2, 'name' => 'asdf', 'component_id'=>'root_page1-1'),
                    array('id'=>3, 'name' => 'asdf', 'component_id'=>'root_page1-2'),
                ),
                'toStringField' => 'name',
            )),
            'component' => 'Kwc_Basic_None_Component'
        );
        return $ret;
    }
}
