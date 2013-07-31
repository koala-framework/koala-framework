<?php
class Kwc_Newsletter_Detail_PreviewController extends Kwc_Mail_PreviewController
{
    protected function _getRecipient()
    {
        $select = new Kwf_Model_Select();
        $select->whereEquals('id', $this->_getParam('recipientId'));
        $row = Kwf_Model_Abstract::getInstance($this->_getParam('subscribeModel'))->getRow($select);
        return $row;
    }

    protected function _getMailComponent()
    {
        $component = Kwf_Component_Data_Root::getInstance()->getComponentByDbId(
            $this->_getParam('componentId') . '-mail', 
            array('ignoreVisible' => true)
        );
        return $component->getComponent();
    }
}
