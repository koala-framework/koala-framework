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
        $mailComponent = $this->_getMailComponent();
        $rs = $mailComponent->getComponent()->getRecipientSources();
        foreach(array_keys($rs) as $key) {
            if (isset($rs[$key]['select']) && ($rs[$key]['model'] == get_class($this->_getModel()))) {
                $ret->merge($rs[$key]['select']);
            }
        }
        return $ret;
    }

    protected function _getMailComponent()
    {
        $mailComponent = Kwf_Component_Data_Root::getInstance()->getComponentByDbId(
            $this->_getParam('componentId') . '_mail',
            array('ignoreVisible' => true)
        );
        return $mailComponent;
    }
}
