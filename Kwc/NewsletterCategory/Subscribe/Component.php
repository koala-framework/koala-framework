<?php
class Kwc_NewsletterCategory_Subscribe_Component extends Kwc_Newsletter_Subscribe_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['generators']['child']['component']['mail'] = 'Kwc_NewsletterCategory_Subscribe_Mail_Component';
        $ret['extConfig'] = 'Kwc_NewsletterCategory_Subscribe_ExtConfig';
        return $ret;
    }

    protected function _initForm()
    {
        $this->_form = new Kwc_NewsletterCategory_Subscribe_FrontendForm(
            'form', $this->getData()->componentId
        );
    }
}
