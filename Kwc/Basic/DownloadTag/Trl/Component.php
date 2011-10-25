<?php
class Kwc_Basic_DownloadTag_Trl_Component extends Kwc_Basic_LinkTag_Abstract_Trl_Component
{
    public static function getSettings($masterComponentClass)
    {
        $ret = parent::getSettings($masterComponentClass);
        $ret['generators']['download'] = array(
            'class' => 'Kwf_Component_Generator_Static',
            'component' => $masterComponentClass
        );
        $ret['ownModel'] = 'Kwf_Component_FieldModel';
        $ret['dataClass'] = 'Kwc_Basic_DownloadTag_Trl_Data';
        return $ret;
    }
}
