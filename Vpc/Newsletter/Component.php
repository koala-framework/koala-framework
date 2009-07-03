<?php
class Vpc_Newsletter_Component extends Vpc_Directories_ItemPage_Directory_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['generators']['detail']['component'] = 'Vpc_Newsletter_Detail_Component';
//        $ret['generators']['detail']['dbIdShortcut'] = 'newsletter_';

        $ret['modelname'] = 'Vpc_Newsletter_Model';
        $ret['flags']['hasResources'] = true;
        $ret['componentName'] = trlVps('Newsletter');
        return $ret;
    }
}
