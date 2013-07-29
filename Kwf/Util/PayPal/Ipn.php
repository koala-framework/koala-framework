<?php
class Kwf_Util_PayPal_Ipn
{
    public static function dispatch($logModel = 'Kwf_Util_PayPal_Ipn_LogModel')
    {
        $url = Kwf_Setup::getRequestPath();
        if ($url != '/paypal_ipn') return;

        self::process($logModel);

        echo 'OK';
        exit;
    }

    public static function process($logModel = 'Kwf_Util_PayPal_Ipn_LogModel')
    {
        if (Kwf_Setup::getConfigSection()=='production' || !isset($_GET['dontValidate'])) {

            $req = 'cmd=_notify-validate';

            foreach ($_POST as $key => $value) {
                if(get_magic_quotes_gpc() == 1) {
                    $value = stripslashes($value);
                }
                $value = urlencode($value);
                $req .= "&$key=$value";
            }

            // post back to PayPal system to validate
            if (isset($_POST['test_ipn']) && $_POST['test_ipn']) {
                $domain = 'www.sandbox.paypal.com';
            } else {
                $domain = 'www.paypal.com';
            }
            // post back to PayPal system to validate
            $header  = "POST /cgi-bin/webscr HTTP/1.1\r\n";
            $header .= "Content-Type: application/x-www-form-urlencoded\r\n";
            $header .= "Host: $domain\r\n";
            $header .= "Content-Length: " . strlen($req) . "\r\n";
            $header .= "Connection: close\r\n\r\n";
            $fp = fsockopen ('ssl://' . $domain, 443, $errno, $errstr, 30);

            if (!$fp) {
                throw new Kwf_Exception("Http error in Ipn validation");
            } else {
                fputs ($fp, $header . $req);
                $res = '';
                while (!feof($fp)) {
                    $res .= fgets ($fp, 1024);
                }
                fclose ($fp);
            }
        } else {
            $res = 'VERIFIED';
        }

        if (trim($res) == "VERIFIED") {
            // TODO:
            // Check the payment_status is Completed
            // Check that txn_id has not been previously processed
            // Check that receiver_email is your Primary PayPal email
            // Check that payment_amount/payment_currency are correct
            // Process payment
            //mail('ns@vivid-planet.com', "Live-VERIFIED IPN", print_r($_POST, true));

            $m = Kwf_Model_Abstract::getInstance($logModel);
            $row = $m->createRow();
            foreach ($_REQUEST as $key => $value) {
                $row->$key = utf8_encode($value);
            }
            $row->save();

        } else if (trim($res) == "INVALID") {
            throw new Kwf_Exception("Ipn validation received INVALID $domain");
        } else {
            throw new Kwf_Exception("Ipn validation received something strange: $res");
        }
    }
}
