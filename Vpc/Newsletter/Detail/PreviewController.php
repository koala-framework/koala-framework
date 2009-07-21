<?php
class Vpc_Newsletter_Detail_PreviewController extends Vpc_Mail_PreviewController
{
    private function _getQueueRow()
    {
        $model = Vps_Model_Abstract::getInstance('Vpc_Newsletter_QueueModel');
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