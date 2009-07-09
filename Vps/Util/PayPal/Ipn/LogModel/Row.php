<?php
class Vps_Util_PayPal_Ipn_LogModel_Row extends Vps_Model_Db_Row
{
    protected function _postInsert()
    {
        parent::_postInsert();
        $c = Vps_Util_PayPal_Ipn_LogModel::decodeCallback($this->custom);
        if ($c && $c['ipnCallback']) {
            call_user_func($c['ipnCallback'], $this, $c['data']);
        }
    }
}
