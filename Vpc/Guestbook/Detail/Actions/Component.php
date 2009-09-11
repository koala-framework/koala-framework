<?php
class Vpc_Guestbook_Detail_Actions_Component extends Vpc_Posts_Detail_Actions_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        unset($ret['generators']['edit'], $ret['generators']['report'], $ret['generators']['delete']);
        $ret['generators']['quote']['component'] = 'Vpc_Guestbook_Detail_Quote_Component';
        return $ret;
    }
}
