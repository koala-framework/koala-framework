<?php
class Kwc_NewsletterCategory_Detail_RecipientsController extends Kwc_NewsletterCategory_Subscribe_RecipientsController
{
    protected $_buttons = array('add', 'delete', 'saveRecipients');

    protected function _isAllowedComponent()
    {
        return Kwf_Controller_Action::_isAllowedComponent();
    }
}
