<?php
class Kwc_Newsletter_Subscribe_Mail_Component extends Kwc_Mail_Abstract_Component
{
    public static function getSettings($param = null)
    {
        $ret = parent::getSettings($param);
        $ret['recipientSources'] = array(
            'sub' => 'Kwc_Newsletter_Subscribe_Model'
        );
        $ret['viewCache'] = false;

        return $ret;
    }

    public function getTemplateVars(Kwf_Component_Renderer_Abstract $renderer)
    {
        $ret = parent::getTemplateVars($renderer);
        $ret = array_merge($ret, $this->getMailData());
        return $ret;
    }

    public function getSubject(Kwc_Mail_Recipient_Interface $recipient = null)
    {
        return $this->getData()->trlKwf('Newsletter subscription');
    }
}
