<?php
class Kwc_Newsletter_Detail_PreviewController extends Kwc_Mail_PreviewController
{
    protected function _getRecipient()
    {
        $select = new Kwf_Model_Select();
        $select->whereEquals('id', $this->_getParam('recipientId'));
        $rs = $this->_getMailComponent()->getRecipientSources();
        $model = $rs[$this->_getParam('subscribeModelKey')]['model'];
        $row = Kwf_Model_Abstract::getInstance($model)->getRow($select);
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
