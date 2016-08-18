<?php
class Kwc_Newsletter_EditSubscriber_Success_Component extends Kwc_Form_Success_Component
{
    public static function getSettings($param = null)
    {
        $ret = parent::getSettings($param);
        $ret['placeholder']['success'] = trlKwfStatic('Your data has been saved successfully.');
        return $ret;
    }
}
