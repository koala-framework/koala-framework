<?php
class Vpc_Cc_Paragraphs_Root extends Vpc_Root_Abstract
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        unset($ret['generators']['title']);
        unset($ret['generators']['box']);
        $ret['generators']['master'] = array(
            'class' => 'Vpc_Chained_Abstract_MasterGenerator',
            'component' => 'Vpc_Cc_Paragraphs_Master_Component',
        );
        $ret['generators']['slave'] = array(
            'class' => 'Vpc_Chained_Abstract_ChainedGenerator',
            'component' => 'Vpc_Cc_Paragraphs_Slave_Component.Vpc_Cc_Paragraphs_Master_Component',
            'filenameColumn' => 'filename',
            'nameColumn' => 'name',
            'uniqueFilename' => true,
        );
        $ret['childModel'] = new Vpc_Chained_Abstract_ChainedModel(array(
            'master' => 'master',
            'slave' => 'slave'
        ));
        return $ret;
    }
}
