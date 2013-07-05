<?php
class Kwc_Newsletter_Detail_RecipientsController extends Kwc_Newsletter_Subscribe_RecipientsController
{
    protected $_buttons = array('saveRecipients', 'removeRecipients');

    protected function _initColumns()
    {
        parent::_initColumns();
        unset($this->_columns['edit']);
    }

    protected function _isAllowedComponent()
    {
        return Kwf_Controller_Action::_isAllowedComponent();
    }

    protected function _getSelect()
    {
        $ret = parent::_getSelect();
        if ($this->_model->hasColumn('unsubscribed')) {
            $ret->whereEquals('unsubscribed', false);
        }
        if ($this->_model->hasColumn('activated')) {
            $ret->whereEquals('activated', true);
        }
        return $ret;
    }
}
