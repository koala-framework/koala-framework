<?php
class Vpc_Guestbook_Detail_Quote_Component extends Vpc_Posts_Detail_Quote_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['componentName'] = 'Quote';
        $ret['generators']['child']['component']['form'] = 'Vpc_Guestbook_Detail_Quote_Form_Component';
        $ret['plugins'] = array();
        return $ret;
    }
}
