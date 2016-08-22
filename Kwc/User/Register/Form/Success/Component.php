<?php
class Kwc_User_Register_Form_Success_Component extends Kwc_Form_Success_Component
{
    public static function getSettings($param = null)
    {
        $ret = parent::getSettings($param);
        $ret['placeholder']['success'] = trlKwfStatic('Your Account has been created successfully. You will receive a confirmation e-mail soon.');
        return $ret;
    }

}
