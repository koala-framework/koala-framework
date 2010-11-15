<?php
class Vps_Component_Plugin_AccessByMail_Form_Success_Component extends Vpc_Form_Success_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['placeholder']['success'] = trlVpsStatic('The Form has been submitted successfully. You will receive an E-Mail with the access link to the protected area.');
        return $ret;
    }
}
