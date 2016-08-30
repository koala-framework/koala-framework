<?php
class Kwc_Feedback_Form_Trl_Component extends Kwc_Form_Trl_Component
{
    public static function getSettings($masterComponentClass = null)
    {
        $ret = parent::getSettings($masterComponentClass);
        $ret['generators']['child']['component'] = 'Kwc_Feedback_Form_Trl_Form_Component';
        return $ret;
    }
}
