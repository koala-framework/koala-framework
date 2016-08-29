<?php
class Kwc_Guestbook_Detail_Quote_Component extends Kwc_Posts_Detail_Quote_Component
{
    public static function getSettings($param = null)
    {
        $ret = parent::getSettings($param);
        $ret['componentName'] = 'Quote';
        $ret['generators']['child']['component']['form'] = 'Kwc_Guestbook_Detail_Quote_Form_Component';
        $ret['plugins'] = array();
        return $ret;
    }
}
