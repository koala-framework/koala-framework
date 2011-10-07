<?php
class Kwf_Crm_Customer_Model_Row_Customer extends Kwf_Model_Proxy_Row
{
    protected function _afterInsert()
    {
        parent::_afterInsert();
        $r = Kwf_Model_Abstract::getInstance('Kwf_Crm_Customer_Model_Comments')->createRow();
        $r->customer_id = $this->id;
        $authedUser = Kwf_Registry::get('userModel')->getAuthedUser();
        $r->value = trlKwf('Customer added by {0}', $authedUser ? $authedUser->__toString() : 'Import');
        $r->save();
    }
}