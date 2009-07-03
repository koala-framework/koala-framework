<?php
class Vps_Util_PayPal_Ipn_LogModel extends Vps_Model_Db
{
    const HASH_CODE = '34sdkfakewrieif';
    protected $_table = 'paypal_ipn_log';
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
    public static function getEncodedCallback($class, $method, $params = array())
    {
        $ret = 'vpsProcessIpnEntry:';
        $data = array(
            'class' => $class,
            'method' => $method,
            'params' => $params
        );
        $data = serialize($data);
        $ret .= substr(md5($data.self::HASH_CODE), 0, 10);
        $ret .= $data;
        return $ret;
    }
}
