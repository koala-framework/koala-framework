<?php
class Vpc_Newsletter_QueueRow extends Vps_Model_Proxy_Row
{
    public function getRecipient()
    {
        $modelname = $this->recipient_model;
        if (!is_instance_of($modelname, 'Vps_Model_Abstract'))
            throw new Vps_Exception("RecipientModel for id {$this->id} has to be a model.");
        $recipientModel = Vps_Model_Abstract::getInstance($modelname);
        return $recipientModel->getRow($this->recipient_id);
    }
}