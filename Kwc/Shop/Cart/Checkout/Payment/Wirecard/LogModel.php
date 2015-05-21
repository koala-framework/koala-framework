<?php
class Kwc_Shop_Cart_Checkout_Payment_Wirecard_LogModel extends Kwf_Model_Db
{
    protected $_table = 'kwc_wirecard_log';
    protected $_rowClass = 'Kwc_Shop_Cart_Checkout_Payment_Wirecard_LogRow';

    protected function _init()
    {
        parent::_init();
        $this->_siblingModels[] = new Kwf_Model_Field(array(
            'fieldName' => 'data'
        ));
    }

    /**
     * Muss in "custom" von der bezahlung gespeichert werden
     */
    public static function getEncodedCallback($ipnCallback, $data = array())
    {
        $ret = 'kwf:';
        $data = array(
            'data' => $data,
            'cb' => $ipnCallback
        );
        $data = serialize($data);
        $ret .= substr(Kwf_Util_Hash::hash($data), 0, 10);
        $data = base64_encode($data);
        $ret .= $data;
        if (strlen($ret) > 256) {
            throw new Kwf_Exception("Wirecard custom field does not support more than 256 characters");
        }
        return $ret;
    }

    public static function decodeCallback($c)
    {
        if ($c && substr($c, 0, 4) == 'kwf:') {
            $c = substr($c, 4);
            $hash = substr($c, 0, 10);
            $c = substr($c, 10);
            $c = base64_decode($c);
            if (!$c) {
                throw new Kwf_Exception("Invalid kwfProcessIpnEntry: can't base64_decode");
            }
            if (substr(Kwf_Util_Hash::hash($c), 0, 10) != $hash) {
                throw new Kwf_Exception("Invalid kwfProcessIpnEntry: hash not correct");
            }
            $c = @unserialize($c);
            if (!$c) {
                throw new Kwf_Exception("Invalid kwfProcessIpnEntry: can't unserialize");
            }
            return $c;
        }
        return false;
    }
}
