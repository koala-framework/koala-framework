<?php
class Kwf_Util_Wirecard
{
    public static function dispatch($logModel = 'Kwf_Util_PayPal_Ipn_LogModel')
    {
        $url = Kwf_Setup::getRequestPath();
        if ($url != '/wirecard_confirm') return;

        self::process($logModel);

        echo 'OK';
        exit;
    }

    public static function process($logModel = 'Kwf_Util_Wirecard_LogModel', $secret = null)
    {
        Kwf_Exception_Abstract::$logErrors = true; //activate log always, because request comes from wirecard
        ignore_user_abort(true);

        if (!$secret) $secret = Kwf_Registry::get('config')->wirecard->secret;

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

            $e = new Kwf_Exception('Wirecard Transaction Failed: '.$message);
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

                $m = Kwf_Model_Abstract::getInstance($logModel);
                $row = $m->createRow();
                foreach ($order as $i) {
                    if ($i != 'secret') {
                        $row->$i = $_POST[$i];
                    }
                }
                $row->custom = $_POST['custom'];
                $row->save();

                // e.g.  something like
                // checkBasketIntegrety($amount, $currency, $basketId);
                // storeAndCloseBasket($paymentType, $orderNumber, $basketId);

                $message = "Vielen Dank für Ihre Bestellung.";
            }
            else
            {
                // there is something strange. maybe an unauthorized call of this page or a wrong secret
                $e = new Kwf_Exception('Wirecard Transaction Failed: Can\'t verify');
                $e->log();

            }
        }
        else
        {
            // unauthorized call of this page
            $e = new Kwf_Exception('Wirecard Transaction Failed: Invalid Payment Status: '.$paymentState);
            $e->log();
        }
        echo 'Pfeift';
        exit;
    }
}
