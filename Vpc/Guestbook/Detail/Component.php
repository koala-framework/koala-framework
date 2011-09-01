<?php
class Vpc_Guestbook_Detail_Component extends Vpc_Posts_Detail_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['componentName'] = trlVps('Guestbook Detail');
        $ret['generators']['child']['component']['actions'] = 'Vpc_Guestbook_Detail_Actions_Component';
        unset($ret['generators']['child']['component']['signature']);
        $ret['extConfig'] = 'Vps_Component_Abstract_ExtConfig_None';
        $ret['extConfigControllerIndex'] = 'Vps_Component_Abstract_ExtConfig_None';

        return $ret;
    }
}
