<?php
class Vpc_Guestbook_Detail_Component extends Vpc_Posts_Detail_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['componentName'] = trlVps('Guestbook Detail');
        $ret['generators']['child']['component']['actions'] = 'Vpc_Guestbook_Detail_Actions_Component';
        unset($ret['generators']['child']['component']['signature']);

        return $ret;
    }
}
