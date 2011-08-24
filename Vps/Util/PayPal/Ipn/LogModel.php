<?php
class Vps_Util_PayPal_Ipn_LogModel extends Vps_Model_Db
{
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
            'cb' => $ipnCallback
        );
        $data = serialize($data);
        $ret .= substr(Vps_Util_Hash::hash($data), 0, 10);
        $data = base64_encode($data);
        $ret .= $data;
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
                throw new Vps_Exception("Invalid vpsProcessIpnEntry: can't base64_decode");
            }
            if (substr(Vps_Util_Hash::hash($c), 0, 10) != $hash) {
                throw new Vps_Exception("Invalid vpsProcessIpnEntry: hash not correct");
            }
            $c = @unserialize($c);
            if (!$c) {
                throw new Vps_Exception("Invalid vpsProcessIpnEntry: can't unserialize");
            }
            return $c;
        }
        return false;
    }

}
