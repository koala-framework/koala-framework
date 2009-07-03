<?php
class Vps_Util_PayPal_Ipn_LogModel_Row extends Vps_Model_Db_Row
{
    protected function _postInsert()
    {
        parent::_postInsert();
        if ($this->custom && substr($this->custom, 0, 19) == 'vpsProcessIpnEntry:') {
            $c = substr($this->custom, 19);
            $hash = substr($c, 0, 10);
            if (substr(md5(substr($c, 10).Vps_Util_PayPal_Ipn_LogModel::HASH_CODE), 0 10) != $hash) {
                throw new Vps_Exception("Invalid vpsProcessIpnEntry: hash not correct ".$c);
            }
            $c = substr($c, 10);
            $c = @unserialize($c);
            if (!$c) {
                throw new Vps_Exception("Invalid vpsProcessIpnEntry: can't unserialize ".$c);
            }
            call_user_func(array($c['class'], $c['method']), $this, $c['params']);
        }
    }
}
