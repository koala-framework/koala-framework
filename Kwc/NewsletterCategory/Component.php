<?php
class Kwc_NewsletterCategory_Component extends Kwc_Newsletter_Component
{
    public static function getSettings($param = null)
    {
        $ret = parent::getSettings($param);
        $ret['generators']['detail']['component'] =
            'Kwc_NewsletterCategory_Detail_Component';

        // wird von der Mail_Redirect gerendered
        $ret['generators']['editSubscriber']['component'] =
            'Kwc_NewsletterCategory_EditSubscriber_Component';

        $ret['menuConfig'] = 'Kwc_NewsletterCategory_MenuConfig';
        return $ret;
    }
}
