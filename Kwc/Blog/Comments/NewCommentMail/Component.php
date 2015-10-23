<?php
class Kwc_Blog_Comments_NewCommentMail_Component extends Kwc_Mail_Abstract_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['recipientSources'] = array(
            'u' => get_class(Kwf_Registry::get('userModel'))
        );
        $ret['viewCache'] = false;
        return $ret;
    }

    public function getTemplateVars(Kwf_Component_Renderer_Abstract $renderer = null)
    {
        $ret = parent::getTemplateVars($renderer);
        $ret = array_merge($ret, $this->getMailData());
        return $ret;
    }

    public function getSubject(Kwc_Mail_Recipient_Interface $recipient = null)
    {
        return $this->getData()->trlKwf('New comment in your blog');
    }
}
