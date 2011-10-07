<?php
class Vpc_Basic_DownloadTag_Trl_Component extends Vpc_Basic_LinkTag_Abstract_Trl_Component
{
    public static function getSettings($masterComponentClass)
    {
        $ret = parent::getSettings($masterComponentClass);
        $ret['generators']['download'] = array(
            'class' => 'Vps_Component_Generator_Static',
            'component' => $masterComponentClass
        );
        $ret['ownModel'] = 'Vps_Component_FieldModel';
        $ret['dataClass'] = 'Vpc_Basic_DownloadTag_Trl_Data';
        return $ret;
    }
}
