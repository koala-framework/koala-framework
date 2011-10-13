<?php
class Vpc_NewsletterCategory_Subscribe_RecipientController extends Vpc_Newsletter_Subscribe_RecipientController
{
    public function preDispatch()
    {
        $this->_form = new Vpc_NewsletterCategory_EditSubscriber_Form(null, $this->_getParam('newsletterComponentId'));
        parent::preDispatch();
    }
}
