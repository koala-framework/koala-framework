<?php
class Kwc_User_LostPassword_SetPassword_Form_Component
    extends Kwc_User_Activate_Form_Component
{
    public static function getSettings($param = null)
    {
        $ret = parent::getSettings($param);
        $ret['placeholder']['submitButton'] = trlKwfStatic('Set new Password');
        $ret['generators']['child']['component']['success'] =
            'Kwc_User_LostPassword_SetPassword_Form_Success_Component';
        return $ret;
    }

    protected function _getErrorMessage($type)
    {
        if ($type == Kwc_User_Activate_Form_Component::ERROR_ALREADY_ACTIVATED
            || $type == Kwc_User_Activate_Form_Component::ERROR_CODE_WRONG
        ) {
            return trlKwf('Maybe you have already set your password, or the link was not copied correct out of the email.');
        }
        return parent::_getErrorMessage($type);
    }
}
