<?php
class Kwc_Advanced_Team_Component extends Kwc_Abstract_List_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['componentName'] = trlKwf('Team');
        $ret['componentIcon'] = new Kwf_Asset('image');
        $ret['generators']['child']['component'] = 'Kwc_Advanced_Team_Member_Component';
        $ret['generators']['child']['class'] = 'Kwc_Advanced_Team_MemberGenerator';
        $ret['extConfig'] = 'Kwc_Abstract_List_ExtConfigList';

        // möglich zu überschreiben für vcards
        // $ret['defaultVcardValues'] = array();
        return $ret;
    }
}
