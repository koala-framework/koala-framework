<?php
class Kwc_Newsletter_Subscribe_MailEditable_Form extends Kwc_Mail_Form
{
    protected function _initFields()
    {
        parent::_initFields();
        $this->fields->remove($this->fields->getByName('subject'));
    }
}
