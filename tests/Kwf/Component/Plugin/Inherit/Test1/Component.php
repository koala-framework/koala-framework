<?php
class Kwf_Component_Plugin_Inherit_Test1_Component extends Kwc_Abstract
{
    public static function getSettings($param = null)
    {
        $ret = parent::getSettings($param);
        $ret['generators']['downloadTag'] = array(
            'class' => 'Kwf_Component_Generator_Static',
            'component' => 'Kwf_Component_Plugin_Inherit_Test1_DownloadTag_Component',
        );
        return $ret;
    }
}
