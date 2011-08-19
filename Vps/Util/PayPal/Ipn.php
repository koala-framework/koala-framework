<?php
class Vps_Util_PayPal_Ipn
{
    public function dispatch($logModel = 'Vps_Util_PayPal_Ipn_LogModel')
    {
        $url = '';
        if (isset($_SERVER['REDIRECT_URL'])) {
            $url = $_SERVER['REDIRECT_URL'];
        }
        if ($url != '/paypal_ipn') return;

        if (Vps_Setup::getConfigSection()=='production' || !isset($_GET['dontValidate'])) {

            $req = 'cmd=_notify-validate';

            foreach ($_POST as $key => $value) {
                if(get_magic_quotes_gpc() == 1) {
                    $value = stripslashes($value);
                }
                $value = urlencode($value);
                $req .= "&$key=$value";
            }

            // Post back to PayPal to validate
            $header  = "POST /cgi-bin/webscr HTTP/1.0\r\n";
            $header .= "Content-Type: application/x-www-form-urlencoded\r\n";
            $header .= "Content-Length: " . strlen($req) . "\r\n\r\n";
            if (isset($_POST['test_ipn']) && $_POST['test_ipn']) {
                $domain = 'www.sandbox.paypal.com';
            } else {
                $domain = 'www.paypal.com';
            }
            $fp = fsockopen($domain, 80);
            if (!$fp) {
                throw new Vps_Exception("Http error in Ipn validation");
            }
            fputs($fp, $header . $req);
            while (!feof($fp)) {
                $res = fgets ($fp, 1024);
            }
        } else {
            $res = 'VERIFIED';
        }

        if (strcmp($res, "VERIFIED") == 0) {
            // TODO:
            // Check the payment_status is Completed
            // Check that txn_id has not been previously processed
            // Check that receiver_email is your Primary PayPal email
            // Check that payment_amount/payment_currency are correct
            // Process payment
            //mail('ns@vivid-planet.com', "Live-VERIFIED IPN", print_r($_POST, true));

            $m = Vps_Model_Abstract::getInstance($logModel);
            $row = $m->createRow();
            foreach ($_REQUEST as $key => $value) {
                $row->$key = utf8_encode($value);
            }
            $row->save();

        } else if (strcmp ($res, "INVALID") == 0) {
            throw new Vps_Exception("Ipn validation received INVALID $domain");
        } else {
            throw new Vps_Exception("Ipn validation received something strange: $res");
        }

        echo 'OK';
        exit;
    }
}
