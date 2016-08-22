<?php
class Kwf_Component_Generator_StaticPageUnderTable_Page1_Component extends Kwc_Abstract
{
    public static function getSettings($param = null)
    {
        $ret = parent::getSettings($param);
        $ret['generators']['table'] = array(
            'class' => 'Kwf_Component_Generator_Table',
            'model' => new Kwf_Model_FnF(array(
                'data' => array(
                    array('id'=>1),
                    array('id'=>2),
                    array('id'=>3),
                    array('id'=>4),
                )
            )),
            'component' => 'Kwf_Component_Generator_StaticPageUnderTable_Page1_Child_Component'
        );
        return $ret;
    }
}
