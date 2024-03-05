<?php
class Kwf_Util_Wirecard_Request
{
    public static function getCheckoutButton(array $params)
    {
        /*
        $params['amount'] = 123;
        $params['currency'] = 'USD';
        $params['language'] = 'en';
        $params['orderDescription'] = $orderDescription;
        $params['displayText'] = $displayText;
        $params['successURL'] = $successURL;
        //$params['cancelURL'] = $cancelURL;
        //$params['failureURL'] = $failureURL;
        //$params['serviceURL'] = $serviceURL;
        //$params['imageURL'] = $imageURL;
        $params['confirmURL'] = $confirmURL;
        $params['customField1'] = 'shopValue1';
        */


        $qpayURL = "https://www.qenta.com/qpay/init.php";

        $params['secret'] = Kwf_Registry::get('config')->wirecard->secret;
        $params['customerId'] = Kwf_Registry::get('config')->wirecard->customerId;
        if (Kwf_Registry::get('config')->wirecard->shopId) $params['shopId'] = Kwf_Registry::get('config')->wirecard->shopId;
        $params['paymenttype'] = 'CCARD';

        $params['requestFingerprintOrder'] = "";
        $requestFingerprintSeed  = "";
        $exclude = array('cancelURL', 'failureURL', 'serviceURL', 'imageURL', 'requestFingerprintOrder');
        foreach ($params as $key=>$value) {
            if (in_array($key, $exclude)) continue;
            $params['requestFingerprintOrder'] .= "$key,";
            $requestFingerprintSeed  .= $value;
        }
        $params['requestFingerprintOrder'] .= "requestFingerprintOrder";
        $requestFingerprintSeed  .= $params['requestFingerprintOrder'];
        $params['requestFingerprint'] = md5($requestFingerprintSeed);

        $ret = "<form action=\"$qpayURL\" method=\"post\" name=\"form\">\n";
        $exclude = array('cancelURL', 'failureURL', 'serviceURL', 'imageURL');
        foreach ($params as $key=>$value) {
            if ($key == 'secret') continue;
            $ret .= "<input type=\"hidden\" name=\"$key\" value=\"".Kwf_Util_HtmlSpecialChars::filter($value)."\" />\n";
        }
        $ret .= "<input type=\"submit\" value=\"Payment\" />\n";
        $ret .= "</form>\n";

        return $ret;
    }
}
