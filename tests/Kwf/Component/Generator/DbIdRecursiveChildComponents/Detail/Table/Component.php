<?php
class Kwf_Component_Generator_DbIdRecursiveChildComponents_Detail_Table_Component extends Kwc_Abstract
{
    public static function getSettings($param = null)
    {
        $ret = parent::getSettings($param);
        $ret['generators']['item'] = array(
            'class' => 'Kwf_Component_Generator_Table',
            'component' => 'Kwf_Component_Generator_DbIdRecursiveChildComponents_Detail_Table_Item_Component',
            'model' => new Kwf_Model_FnF(array(
                'data' => array(
                    array('id' => 1, 'component_id' => 'foo-2-table'),
                    array('id' => 2, 'component_id' => 'foo-1-table'),
                    array('id' => 3, 'component_id' => 'foo-1-table'),
                ),
            ))
        );
        return $ret;
    }
}
