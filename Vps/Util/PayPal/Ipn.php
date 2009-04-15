<?php
class Vps_Util_PayPal_Ipn
{
    public function dispatch()
    {
        $url = '';
        if (isset($_SERVER['REDIRECT_URL'])) {
            $url = $_SERVER['REDIRECT_URL'];
        }
        if ($url != '/paypal_ipn') return;

        $m = Vps_Model_Abstract::getInstance('Vps_Util_PayPal_Ipn_LogModel');
        $row = $m->createRow();
        if (isset($_GET['sandbox'])) {
            $row->sandbox = 1;
        }
        foreach ($_POST as $key => $value) {
            $row->$key = $value;
        }
        $row->save();

        $email = 'ns@vivid-planet.com';
        $header = "";
        $emailtext = "";

        $req = 'cmd=_notify-validate';

        foreach ($_POST as $key => $value) {
            if(get_magic_quotes_gpc() == 1) {
                $value = stripslashes($value);
            }
            $value = urlencode($value);
            $req .= "&$key=$value";
        }

        // Post back to PayPal to validate
        $header .= "POST /cgi-bin/webscr HTTP/1.0\r\n";
        $header .= "Content-Type: application/x-www-form-urlencoded\r\n";
        $header .= "Content-Length: " . strlen($req) . "\r\n\r\n";
        if (isset($_GET['sandbox'])) {
            $domain = 'www.sandbox.paypal.com';
        } else {
            $domain = 'www.paypal.com';
        }
        $fp = fsockopen($domain, 80);
        if (!$fp) {
            throw new Vps_Exception("Http error in Ipn validation");
        } else {
            fputs($fp, $header . $req);
            while (!feof($fp)) {
                $res = fgets ($fp, 1024);
            }
            if (strcmp($res, "VERIFIED") == 0) {
                // TODO:
                // Check the payment_status is Completed
                // Check that txn_id has not been previously processed
                // Check that receiver_email is your Primary PayPal email
                // Check that payment_amount/payment_currency are correct
                // Process payment
                foreach ($_POST as $key => $value){
                    $emailtext .= $key . " = " .$value ."\n\n";
                }
                mail($email, "Live-VERIFIED IPN", $emailtext . "\n\n" . $req);

                $m = Vps_Model_Abstract::getInstance('Vps_Util_PayPal_Ipn_LogModel');
                $row = $m->createRow();
                if (isset($_GET['sandbox'])) {
                    $row->sandbox = 1;
                }
                foreach ($_POST as $key => $value) {
                    $row->$key = $value;
                }
                $row->save();

            } else if (strcmp ($res, "INVALID") == 0) {
                throw new Vps_Exception("Ipn validation received INVALID $domain");
            } else {
                throw new Vps_Exception("Ipn validation received something strange");
            }
        }
        echo 'OK';
        exit;
    }
}
