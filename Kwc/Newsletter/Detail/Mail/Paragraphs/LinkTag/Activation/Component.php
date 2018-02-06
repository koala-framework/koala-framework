<?php
class Kwc_Newsletter_Detail_Mail_Paragraphs_LinkTag_Activation_Component
    extends Kwc_Newsletter_Detail_Mail_Paragraphs_LinkTag_Abstract_Component
{
    public static function getSettings($param = null)
    {
        $ret = parent::getSettings($param);
        $ret['componentName'] = trlKwfStatic('Activate Subscription');
        return $ret;
    }

    protected function _getTargetComponent() {
        return Kwf_Component_Data_Root::getInstance()->getComponentByClass(
            'Kwc_Newsletter_Subscribe_DoubleOptIn_Component', array('subroot' => $this->_getNewsletterComponent()->getSubroot())
        );
    }
}
