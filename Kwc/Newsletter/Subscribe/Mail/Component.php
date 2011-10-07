<?php
class Kwc_Newsletter_Subscribe_Mail_Component extends Kwc_Mail_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        unset($ret['generators']['content']);
        $ret['generators']['redirect'] = array(
            'class' => 'Kwf_Component_Generator_Page_Static',
            'component' => 'Kwc_Mail_Redirect_Component',
            'name' => 'r'
        );

        $ret['default']['subject'] = trlKwf('Newsletter subscription');

        $ret['recipientSources'] = array(
            'sub' => 'Kwc_Newsletter_Subscribe_Model'
        );

        return $ret;
    }

    public function getTemplateVars()
    {
        $ret = parent::getTemplateVars();
        $ret = array_merge($ret, $this->getMailData());
        return $ret;
    }

    public function getSubject(Kwc_Mail_Recipient_Interface $recipient = null)
    {
        return trlKwf('Newsletter subscription');
    }
}
