<?php
class Vpc_NewsletterCategory_Detail_RecipientsController extends Vpc_NewsletterCategory_Subscribe_RecipientsController
{
    protected $_buttons = array('add', 'delete', 'saveRecipients');

    protected function _isAllowedComponent()
    {
        return Vps_Controller_Action::_isAllowedComponent();
    }
}
