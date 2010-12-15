<?php
class Vpc_NewsletterCategory_Component extends Vpc_Newsletter_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['generators']['detail']['component'] =
            'Vpc_NewsletterCategory_Detail_Component';

        // wird von der Mail_Redirect gerendered
        $ret['generators']['editSubscriber']['component'] =
            'Vpc_NewsletterCategory_EditSubscriber_Component';

        return $ret;
    }
}
