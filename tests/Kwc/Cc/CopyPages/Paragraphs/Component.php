<?php
class Kwc_Cc_CopyPages_Paragraphs_Component extends Kwc_Paragraphs_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['childModel'] = new Kwf_Model_FnF(array(
            'data' => array(
                array('id'=>1, 'component_id'=>'1', 'component' => 'test', 'visible'=>true),
                array('id'=>2, 'component_id'=>'1', 'component' => 'empty', 'visible'=>true)
            ),
        ));
        $ret['generators']['paragraphs']['component'] = array(
            'empty' => 'Kwc_Basic_Empty_Component',
            'test' => 'Kwc_Cc_CopyPages_TestComponent_Component',
        );
        return $ret;
    }
}
