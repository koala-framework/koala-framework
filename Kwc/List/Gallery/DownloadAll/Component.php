<?php
/*
 * Add to List_Gallery_Component to show Download All link:
 * $ret['generators']['downloadAll'] = array(
 *   'class' => 'Kwf_Component_Generator_Static',
 *   'component' => 'Kwc_List_Gallery_DownloadAll_Component'
 * );
 * 
 */
class Kwc_List_Gallery_DownloadAll_Component extends Kwc_Abstract
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['generators']['download'] = array(
            'class' => 'Kwf_Component_Generator_Page_Static',
            'component' => 'Kwc_List_Gallery_DownloadAll_Download_Component',
            'name' => 'download'
        );
        return $ret;
    }
    
    public function getTemplateVars() {
        $ret = parent::getTemplateVars();
        $ret['download'] = $this->getData()->getChildComponent('_download');
        return $ret;
    }
}
