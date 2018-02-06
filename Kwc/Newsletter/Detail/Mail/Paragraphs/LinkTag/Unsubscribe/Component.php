<?php
class Kwc_Newsletter_Detail_Mail_Paragraphs_LinkTag_Unsubscribe_Component
    extends Kwc_Newsletter_Detail_Mail_Paragraphs_LinkTag_Abstract_Component
{
    public static function getSettings($param = null)
    {
        $ret = parent::getSettings($param);
        $ret['componentName'] = trlKwfStatic('Unsubscribe Newsletter');
        return $ret;
    }

    protected function _getTargetComponent()
    {
        return $this->_getNewsletterComponent()->getChildComponent('_unsubscribe');
    }
}
