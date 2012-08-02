<?php
class Kwc_Newsletter_EditSubscriber_Success_Component extends Kwc_Form_Success_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['placeholder']['success'] = trlKwfStatic('Your data has been saved successfully.');
        return $ret;
    }
}
