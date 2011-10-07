<?php
class Vps_Crm_Customer_Model_Row_Customer extends Vps_Model_Proxy_Row
{
    protected function _afterInsert()
    {
        parent::_afterInsert();
        $r = Vps_Model_Abstract::getInstance('Vps_Crm_Customer_Model_Comments')->createRow();
        $r->customer_id = $this->id;
        $authedUser = Vps_Registry::get('userModel')->getAuthedUser();
        $r->value = trlVps('Customer added by {0}', $authedUser ? $authedUser->__toString() : 'Import');
        $r->save();
    }
}