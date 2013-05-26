<?php
class Kwc_Composite_Downloads_Component extends Kwc_Abstract_List_Component
{
    public static function getSettings()
    {
        $settings = parent::getSettings();
        $settings['generators']['child']['component'] = 'Kwc_Basic_Download_Component';
        $settings['componentIcon'] = new Kwf_Asset('disk');
        $settings['componentName'] = trlKwf('Downloads');
        $settings['childModel'] = 'Kwc_Composite_Downloads_Model';

        return $settings;
    }
}
