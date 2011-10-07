<?php
class Vpc_User_LostPassword_SetPassword_Form_Component
    extends Vpc_User_Activate_Form_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['placeholder']['submitButton'] = trlVpsStatic('Set new Password');
        $ret['generators']['child']['component']['success'] =
            'Vpc_User_LostPassword_SetPassword_Form_Success_Component';
        return $ret;
    }

    protected function _getErrorMessage($type)
    {
        if ($type == Vpc_User_Activate_Form_Component::ERROR_ALREADY_ACTIVATED
            || $type == Vpc_User_Activate_Form_Component::ERROR_CODE_WRONG
        ) {
            return trlVps('Maybe you have already set your password, or the link was not copied correct out of the email.');
        }
        return null;
    }
}
