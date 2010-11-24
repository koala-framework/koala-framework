<?php
class Vpc_NewsletterCategory_Subscribe_Component extends Vpc_Newsletter_Subscribe_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['generators']['child']['component']['mail'] = 'Vpc_NewsletterCategory_Subscribe_Mail_Component';
        return $ret;
    }

    protected function _initForm()
    {
        $this->_form = new Vpc_NewsletterCategory_Subscribe_FrontendForm(
            'form', $this->getData()->componentId
        );
    }
}
