<?php
class Kwc_Newsletter_Subscribe_DoubleOptIn_Component extends Kwc_Form_Success_Component
{
    public static function getSettings($param = null)
    {
        $ret = parent::getSettings($param);
        $ret['placeholder']['success'] = trlKwfStatic('Your E-Mail address has been verified. You will receive our newsletters in future.');
        $ret['flags']['processInput'] = true;
        $ret['flags']['passMailRecipient'] = true;
        return $ret;
    }

    public function processInput(array $postData)
    {
        if (!isset($postData['recipient'])) {
            throw new Kwf_Exception_NotFound();
        }
        $recipient = Kwc_Mail_Redirect_Component::parseRecipientParam($postData['recipient']);

        if (!$recipient->activated) {
            $recipient->unsubscribed = 0;
            $recipient->activated = 1;

            $recipient->setLogSource($this->getData()->getAbsoluteUrl());
            $recipient->writeLog($this->getData()->trlKwf('Activated'), 'activated');

            $recipient->save();
        }
    }
}
