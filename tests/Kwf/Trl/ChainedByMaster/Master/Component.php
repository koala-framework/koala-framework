<?php
class Kwf_Trl_ChainedByMaster_Master_Component extends Kwc_Abstract
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['generators']['page'] = array(
            'class' => 'Kwc_Root_Category_Generator',
            'inherit' => true,
            'component' => array(
                'empty' => 'Kwc_Basic_None_Component',
                'link' => 'Kwc_Basic_LinkTag_Component',
                'firstChildPage' => 'Kwc_Basic_LinkTag_FirstChildPage_Component'
            ),
            'model' => 'Kwf_Trl_ChainedByMaster_Master_Model'
        );
        $ret['generators']['99'] = array(
            'class' => 'Kwf_Component_Generator_Static',
            'component' => 'Kwc_Basic_None_Component'
        );
        $ret['flags']['chainedType'] = 'Trl';
        return $ret;
    }
}
