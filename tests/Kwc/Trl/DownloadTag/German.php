<?php
class Kwc_Trl_DownloadTag_German extends Kwc_Abstract
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['generators']['test1'] = array(
            'class' => 'Kwf_Component_Generator_Page_Static',
            'component' => 'Kwc_Trl_DownloadTag_DownloadTag_Component',
            'name' => 'test1',
        );
        $ret['generators']['test2'] = array(
            'class' => 'Kwf_Component_Generator_Page_Static',
            'component' => 'Kwc_Trl_DownloadTag_DownloadTag_Component',
            'name' => 'test2',
        );
        return $ret;
    }
}
