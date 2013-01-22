<?php
class Kwc_Articles_Directory_Component extends Kwc_Directories_ItemPage_Directory_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['generators']['child']['component']['view'] = 'Kwc_Articles_Directory_View_Component';

        $ret['childModel'] = 'Kwc_Articles_Directory_Model';

        //not allowed to process in pageTree
        $ret['extConfig'] = 'Kwf_Component_Abstract_ExtConfig_None';

        $ret['contentSender'] = 'Kwc_Articles_Directory_ContentSender';
        return $ret;
    }
}
