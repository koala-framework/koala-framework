<?php
class Kwc_Cc_Composite_Root extends Kwc_Root_Abstract
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        unset($ret['generators']['title']);
        unset($ret['generators']['box']);
        $ret['generators']['master'] = array(
            'class' => 'Kwc_Chained_Abstract_MasterGenerator',
            'component' => 'Kwc_Cc_Composite_Master_Component',
        );
        $ret['generators']['slave'] = array(
            'class' => 'Kwc_Chained_Trl_ChainedGenerator',
            'component' => 'Kwc_Cc_Composite_Slave_Component.Kwc_Cc_Composite_Master_Component',
            'filenameColumn' => 'filename',
            'nameColumn' => 'name',
            'uniqueFilename' => true,
        );
        $ret['childModel'] = new Kwc_Trl_RootModel(array(
            'master' => 'master',
            'slave' => 'slave'
        ));
        return $ret;
    }
}
