<?php
class Vpc_Basic_DownloadTagBehindLogin_Root extends Vps_Component_NoCategoriesRoot
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['generators']['test'] = array(
            'class' => 'Vps_Component_Generator_Page_Static',
            'component' => 'Vpc_Basic_DownloadTagBehindLogin_Download'
        );
        unset($ret['generators']['page']);
        unset($ret['generators']['title']);
        unset($ret['generators']['box']);
        return $ret;
    }
}
