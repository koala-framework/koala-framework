<?php
class Vpc_Newsletter_Subscribe_Mail_Component extends Vpc_Mail_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        unset($ret['generators']['content']);

        $ret['recipientSources'] = array(
            'sub' => 'Vpc_Newsletter_Subscribe_Model'
        );

        return $ret;
    }

    public function getTemplateVars()
    {
        $ret = parent::getTemplateVars();
        $ret = array_merge($ret, $this->getMailData());
        return $ret;
    }

    public function getSubject(Vpc_Mail_Recipient_Interface $recipient = null)
    {
        return trlVps('Newsletter subscription');
    }
}
