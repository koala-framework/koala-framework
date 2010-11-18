<?php
class Vpc_Advanced_Team_Member_Component extends Vpc_Abstract_Composite_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['componentName'] = trlVps('Team member');

        $ret['generators']['child']['component']['image'] =
            'Vpc_Advanced_Team_Member_Image_Component';
        $ret['generators']['child']['component']['data'] =
            'Vpc_Advanced_Team_Member_Data_Component';
        return $ret;
    }
}
