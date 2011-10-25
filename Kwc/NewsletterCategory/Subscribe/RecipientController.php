<?php
class Kwc_NewsletterCategory_Subscribe_RecipientController extends Kwc_Newsletter_Subscribe_RecipientController
{
    public function preDispatch()
    {
        $this->_form = new Kwc_NewsletterCategory_EditSubscriber_Form(null, $this->_getParam('newsletterComponentId'));
        parent::preDispatch();
    }
}
