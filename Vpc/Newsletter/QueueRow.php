<?php
class Vpc_Newsletter_QueueRow extends Vps_Model_Proxy_Row
{
    public function getRecipient()
    {
        $modelname = $this->recipient_model;
        if (is_instance_of($modelname, 'Vps_Model_Abstract')) {
            $row = Vps_Model_Abstract::getInstance($modelname)->getRow($this->recipient_id);
        } else if (is_instance_of($modelname, 'Zend_Db_Table_Abstract')) {
            $row = Vps_Dao::getTable($modelname)->find($this->recipient_id)->current();
        } else {
            throw new Vps_Exception("Recipient-Model for id {$this->id} has to be a model or a table.");
        }
        if ($row && !$row instanceof Vpc_Mail_Recipient_Interface) {
            throw new Vps_Exception("Recipient-Row has to implement Vpc_Mail_Recipient_Interface");
        }
        return $row;
    }
}