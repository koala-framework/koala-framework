<?php
class Kwc_Newsletter_Detail_PreviewController extends Kwc_Mail_PreviewController
{
    private function _getQueueRow()
    {
        $model = Kwf_Model_Abstract::getInstance('Kwc_Newsletter_QueueModel');
        return $model->getRow($this->_getParam('id'));
    }

    protected function _getRecipient()
    {
        return $this->_getQueueRow()->getRecipient();
    }

    protected function _getMailComponent()
    {
        return $this->_getQueueRow()->getParentRow('Newsletter')->getMailComponent();
    }
}