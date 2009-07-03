<?php
class Vpc_Newsletter_Detail_MailController extends Vps_Controller_Action
{
    public function jsonDataAction()
    {
        $model = Vps_Model_Abstract::getInstance('Vpc_Newsletter_QueueModel');
        $row = $model->getRow($this->_getParam('id'));
        $mailModel = Vps_Model_Abstract::getInstance($row->recipient_model);
        $recipient = $mailModel->getRow($row->recipient_id);

        $component = Vps_Component_Data_Root::getInstance()
            ->getComponentByDbId($this->_getParam('componentId'), array('ignoreVisible' => true))
            ->getChildComponent('-mail');

        $this->view->html = $component->getComponent()->getHtml($recipient);
    }
}