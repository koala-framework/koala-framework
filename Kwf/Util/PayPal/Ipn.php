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

            // post back to PayPal system to validate
            if (isset($_POST['test_ipn']) && $_POST['test_ipn']) {
                $domain = 'ipnpb.sandbox.paypal.com';
            } else {
                $domain = 'ipnpb.paypal.com';
            }

            // post back to PayPal system to validate
            $client = new Zend_Http_Client('https://'.$domain.'/cgi-bin/webscr');
            $rawBody = 'cmd=_notify-validate';
            foreach ($_POST as $key => $value) {
                $rawBody .= "&$key=" . $value;
            }
            $client->setRawData($rawBody);
            $response = $client->request(Zend_Http_Client::POST);
            $res = trim($response->getBody());

        } else {
            $res = 'VERIFIED';
        }

        if ($res == "VERIFIED") {
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

        } else if ($res == "INVALID") {
            throw new Kwf_Exception("Ipn validation received INVALID $domain");
        } else {
            $msg = "Ipn validation received something strange: $res\n";
            if (isset($response)) $msg .= $response->asString();
            throw new Kwf_Exception($msg);
        }
    }
}
