<?php
class Vps_Trl_ChainedByMaster_Master_Component extends Vpc_Abstract
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['generators']['page'] = array(
            'class' => 'Vpc_Root_Category_Generator',
            'inherit' => true,
            'component' => array(
                'empty' => 'Vpc_Basic_Empty_Component',
                'link' => 'Vpc_Basic_LinkTag_Component',
                'firstChildPage' => 'Vpc_Basic_LinkTag_FirstChildPage_Component'
            ),
            'model' => 'Vps_Trl_ChainedByMaster_Master_Model'
        );
        $ret['generators']['99'] = array(
            'class' => 'Vps_Component_Generator_Static',
            'component' => 'Vpc_Basic_Empty_Component'
        );
        $ret['flags']['hasLanguage'] = true;
        $ret['flags']['chainedType'] = 'Trl';
        return $ret;
    }
}
