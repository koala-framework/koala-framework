<?php
class Kwc_Guestbook_Detail_Component extends Kwc_Posts_Detail_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['componentName'] = trlKwf('Guestbook Detail');
        $ret['generators']['child']['component']['actions'] = 'Kwc_Guestbook_Detail_Actions_Component';
        unset($ret['generators']['child']['component']['signature']);
        $ret['extConfig'] = 'Kwf_Component_Abstract_ExtConfig_None';
        $ret['extConfigControllerIndex'] = 'Kwf_Component_Abstract_ExtConfig_None';

        return $ret;
    }
}
