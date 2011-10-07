<?php
class Vps_Crm_Customer_Model_Row_Comment extends Vps_Model_Proxy_Row
{
    protected function _beforeInsert()
    {
        parent::_beforeInsert();
        $this->insert_date = date('Y-m-d H:i:s');
    }
}