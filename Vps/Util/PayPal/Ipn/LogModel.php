<?php
class Vps_Util_PayPal_Ipn_LogModel extends Vps_Model_Db
{
    const HASH_CODE = '34sdkfakewrieif';
    protected $_table = 'paypal_ipn_log';
    protected $_rowClass = 'Vps_Util_PayPal_Ipn_LogModel_Row';
    protected function _init()
    {
        parent::_init();
        $this->_siblingModels[] = new Vps_Model_Field(array(
            'fieldName' => 'data'
        ));
    }

    /**
     * Muss in "custom" von der bezahlung gespeichert werden
     */
    public static function getEncodedCallback($ipnCallback, $data = array())
    {
        $ret = 'vps:';
        $data = array(
            'data' => $data,
            'ipnCallback' => $ipnCallback
        );
        $data = serialize($data);
        $ret .= substr(md5($data.self::HASH_CODE), 0, 10);
        $ret .= base64_encode($data);
        return $ret;
    }

    public static function decodeCallback($c)
    {
        if ($c && substr($c, 0, 4) == 'vps:') {
            $c = substr($c, 4);
            $hash = substr($c, 0, 10);
            $c = substr($c, 10);
            $c = base64_decode($c);
            if (!$c) {
                throw new Vps_Exception("Invalid vpsProcessIpnEntry: can't base64_decode ".$c);
            }
            if (substr(md5($c.Vps_Util_PayPal_Ipn_LogModel::HASH_CODE), 0, 10) != $hash) {
                throw new Vps_Exception("Invalid vpsProcessIpnEntry: hash not correct ".$c);
            }
            $c = @unserialize($c);
            if (!$c) {
                throw new Vps_Exception("Invalid vpsProcessIpnEntry: can't unserialize ".$c);
            }
            return $c;
        }
        return false;
    }

}
