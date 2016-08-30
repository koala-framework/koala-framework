<?php
class Kwc_Cc_RootWithTrl_Master_Component extends Kwc_Abstract
{
    public static function getSettings($param = null)
    {
        $ret = parent::getSettings($param);
        $ret['generators']['master'] = array(
            'class' => 'Kwc_Chained_Trl_MasterGenerator',
            'component' => 'Kwc_Cc_RootWithTrl_Master_Master_Component',
        );
        $ret['generators']['chained'] = array(
            'class' => 'Kwc_Chained_Trl_ChainedGenerator',
            'component' => 'Kwc_Root_TrlRoot_Chained_Component.Kwc_Cc_RootWithTrl_Master_Master_Component',
            'filenameColumn' => 'filename',
            'nameColumn' => 'name',
            'uniqueFilename' => true,
        );
        $ret['childModel'] = new Kwc_Trl_RootModel(array(
            'master' => 'master',
            'slave' => 'slave'
        ));

        $ret['flags']['chainedType'] = 'Cc';
        return $ret;
    }
}