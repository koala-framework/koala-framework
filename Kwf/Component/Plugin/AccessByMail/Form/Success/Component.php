<?php
class Kwf_Component_Plugin_AccessByMail_Form_Success_Component extends Kwc_Form_Success_Component
{
    public static function getSettings($param = null)
    {
        $ret = parent::getSettings($param);
        $ret['placeholder']['success'] = trlKwfStatic('The Form has been submitted successfully. You will receive an E-Mail with the access link to the protected area.');
        return $ret;
    }
}
