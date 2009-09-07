<?php
class Vpc_Newsletter_EditSubscriber_Success_Component extends Vpc_Form_Success_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['placeholder']['success'] = trlVps('Your data has been saved successfully.');
        return $ret;
    }
}
