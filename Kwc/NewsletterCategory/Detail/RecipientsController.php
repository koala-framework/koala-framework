<?php
class Kwc_NewsletterCategory_Detail_RecipientsController extends Kwc_NewsletterCategory_Subscribe_RecipientsController
{
    protected $_buttons = array('saveRecipients', 'removeRecipients');

    protected function _isAllowedComponent()
    {
        return Kwf_Controller_Action::_isAllowedComponent();
    }

    protected function _getSelect()
    {
        $ret = parent::_getSelect();
        $ret->whereEquals('unsubscribed', false);
        return $ret;
    }

    protected function _addPluginSelect($select)
    {
        foreach (Kwf_Component_Data_Root::getInstance()->getPlugins('Kwc_Newsletter_PluginInterface') as $plugin) {
            $plugin->modifyRecipientsSelect($select, Kwc_Newsletter_PluginInterface::RECIPIENTS_GRID_TYPE_ADD_TO_QUEUE);
        }
        return $select;
    }
}
