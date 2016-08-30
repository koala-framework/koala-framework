<?php
class Kwf_Component_Generator_DbIdRecursiveChildComponents_Detail_Cards_Component extends Kwc_Abstract_Cards_Component
{
    public static function getSettings($param = null)
    {
        $ret = parent::getSettings($param);
        $ret['ownModel'] = new Kwf_Model_FnF(array(
            'primaryKey' => 'component_id',
            'data' => array(
                array('component_id'=>'foo-1-cards', 'component'=>'none'),
                array('component_id'=>'foo-2-cards', 'component'=>'card'),
            )
        ));
        $ret['generators']['child']['component'] = array(
            'none' => 'Kwc_Basic_None_Component',
            'card' => 'Kwf_Component_Generator_DbIdRecursiveChildComponents_Detail_Cards_Card_Component',
        );
        return $ret;
    }
}
