<?php
class Vpc_Advanced_Team_Component extends Vpc_Abstract_List_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['componentName'] = trlVps('Team');
        $ret['componentIcon'] = new Vps_Asset('image');
        $ret['generators']['child']['component'] = 'Vpc_Advanced_Team_Member_Component';
        $ret['generators']['child']['class'] = 'Vpc_Advanced_Team_MemberGenerator';

        // möglich zu überschreiben für vcards
        // $ret['defaultVcardValues'] = array();
        return $ret;
    }
}
