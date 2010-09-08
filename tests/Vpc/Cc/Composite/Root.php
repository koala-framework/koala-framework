<?php
class Vpc_Cc_Composite_Root extends Vpc_Root_Abstract
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        unset($ret['generators']['title']);
        unset($ret['generators']['box']);
        $ret['generators']['master'] = array(
            'class' => 'Vpc_Chained_Abstract_MasterGenerator',
            'component' => 'Vpc_Cc_Composite_Master_Component',
        );
        $ret['generators']['slave'] = array(
            'class' => 'Vpc_Chained_Trl_ChainedGenerator',
            'component' => 'Vpc_Cc_Composite_Slave_Component.Vpc_Cc_Composite_Master_Component',
            'filenameColumn' => 'filename',
            'nameColumn' => 'name',
            'uniqueFilename' => true,
        );
        $ret['childModel'] = new Vpc_Root_TrlRoot_Model(array(
            'master' => 'master',
            'slave' => 'slave'
        ));
        return $ret;
    }
}
