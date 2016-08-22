<?php
class Kwc_Guestbook_Detail_Actions_Component extends Kwc_Posts_Detail_Actions_Component
{
    public static function getSettings($param = null)
    {
        $ret = parent::getSettings($param);
        $ret['componentName'] = 'Actions';
        unset($ret['generators']['edit'], $ret['generators']['report'], $ret['generators']['delete']);
        $ret['generators']['quote']['component'] = 'Kwc_Guestbook_Detail_Quote_Component';
        return $ret;
    }
}
