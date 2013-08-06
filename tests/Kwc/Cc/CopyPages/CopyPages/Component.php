<?php
class Kwc_Cc_CopyPages_CopyPages_Component extends Kwc_Chained_CopyPages_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings('Kwc_Cc_CopyPages_Root');
        $ret['ownModel'] = new Kwf_Model_FnF(array(
            'primaryKey' => 'component_id',
            'data' => array(
                array('component_id'=>'2', 'target'=>'1'),
            )
        ));
        $ret['generators']['target']['class'] = 'Kwc_Cc_CopyPages_CopyPages_TargetGenerator';
        $ret['generators']['page']['class'] = 'Kwc_Cc_CopyPages_CopyPages_PagesGenerator';
        return $ret;
    }
}
