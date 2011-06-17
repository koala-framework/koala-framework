<?php
class Vps_Util_Wirecard
{
    public function dispatch($logModel = 'Vps_Util_Wirecard_LogModel')
    {
        $url = '';
        if (isset($_SERVER['REDIRECT_URL'])) {
            $url = $_SERVER['REDIRECT_URL'];
        }
        if ($url != '/wirecard_confirm') return;


        $secret = Vps_Registry::get('config')->wirecard->secret;

        $paymentState = isset($_POST["paymentState"]) ? $_POST["paymentState"] : "";

        if (strcmp($paymentState,"CANCEL") == 0)
        {
            /// the transaction was cancelled.
            $message = "Transaktion wurde abgebrochen.";
        }
        else if (strcmp($paymentState,"FAILURE") == 0)
        {
            // there was something wrong with the initiation or an fatal error during the transaction processing occured
            $message = $_POST["message"];

            $e = new Vps_Exception('Wirecard Transaction Failed: '.$message);
            $e->log();

            $message = "Fehler bei der Initiierung: " . $message;
        }
        else if (strcmp($paymentState,"SUCCESS") == 0)
        {
            $responseFingerprintOrder = $_POST["responseFingerprintOrder"];
            $responseFingerprint = $_POST["responseFingerprint"];

            $str4responseFingerprint = "";
            $mandatoryFingerPrintFields = 0;
            $secretUsed = 0;

            $order = explode(",",$responseFingerprintOrder);
            for ($i = 0; $i < count($order); $i++)
            {
                $key = $order[$i];

                // check if there are enough fields in den responsefingerprint
                if ((strcmp($key, "paymentState")) == 0 && (strlen($_POST[$order[$i]]) > 0))
                {
                    $mandatoryFingerPrintFields++;
                }
                if ((strcmp($key, "orderNumber")) == 0 && (strlen($_POST[$order[$i]]) > 0))
                {
                    $mandatoryFingerPrintFields++;
                }
                if ((strcmp($key, "paymentType")) == 0 && (strlen($_POST[$order[$i]]) > 0))
                {
                    $mandatoryFingerPrintFields++;
                }

                if (strcmp($key, "secret") == 0)
                {
                    $str4responseFingerprint .= $secret;
                    $secretUsed = 1;
                }
                else
                {
                    $str4responseFingerprint .= $_POST[$order[$i]];
                }
            }

            // recalc the fingerprint
            $responseFingerprintCalc = md5($str4responseFingerprint);

            if ((strcmp($responseFingerprintCalc,$responseFingerprint) == 0)
                && ($mandatoryFingerPrintFields == 3)
                && ($secretUsed == 1))
            {
                // everything is ok. store the successfull payment somewhere

                // please store at least the paymentType and the orderNumber additional to the orderinformation,
                // otherwise you will never find the transaction again.

                $m = Vps_Model_Abstract::getInstance($logModel);
                $row = $m->createRow($order);
                $row->txn_type = 'wirecard_payment';
                $row->save();

                // e.g.  something like
                // checkBasketIntegrety($amount, $currency, $basketId);
                // storeAndCloseBasket($paymentType, $orderNumber, $basketId);

                $message = "Vielen Dank für Ihre Bestellung.";
            }
            else
            {
                // there is something strange. maybe an unauthorized call of this page or a wrong secret
                $e = new Vps_Exception('Wirecard Transaction Failed: Can\'t verify');
                $e->log();

            }
        }
        else
        {
            // unauthorized call of this page
            $e = new Vps_Exception('Wirecard Transaction Failed: Invalid Payment Status: '.$paymentState);
            $e->log();
        }
        echo 'Pfeift';
        exit;













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
                $row->$key = $value;
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
