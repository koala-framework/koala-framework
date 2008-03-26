<?php
class Vps_Debug
{
    public static function sendErrorMail($exception, $address)
    {
        if ($exception instanceof Vps_Controller_Action_Web_FileNotFoundException) {
            //404-Fehler nicht als mail verschicken
            return;
        }
        if ($exception instanceof Vps_CustomException) {
            $type = $exception->getType();
        } else {
            $type = get_class($exception);
        }

        $body = $exception->__toString();
        $body .= "\n\nREQUEST_URI: ".$_SERVER['REQUEST_URI'];
        $body .= "\nHTTP_REFERER: ".(isset($_SERVER['HTTP_REFERER'])
                                        ? $_SERVER['HTTP_REFERER'] : '(none)');
        $u = Zend_Registry::get('userModel')->getAuthedUser();
        $body .= "\nUser: ";
        if ($u) {
            $body .= "$u, id $u->id, $u->role";
        } else {
            $body .= "guest";
        }
        $body .= "\n\n------------------\n\n_GET:\n";
        $body .= print_r($_GET, true);
        $body .= "\n\n------------------\n\n_POST:\n";
        $body .= print_r($_POST, true);
        $body .= "\n\n------------------\n\n_SERVER:\n";
        $body .= print_r($_SERVER, true);
        $body .= "\n\n------------------\n\n_SESSION:\n";
        $body .= print_r($_SESSION, true);
        $body .= "\n\n------------------\n\n_FILES:\n";
        $body .= print_r($_FILES, true);
        $body = substr($body, 0, 5000);
        $mail = new Zend_Mail('utf-8');
        $mail->setBodyText($body)
            ->setSubject($_SERVER['HTTP_HOST'] . ': ' . $type);
        $mail->addTo('vperror@vivid-planet.com');
        if (is_string($address)) $address = array($address);
        foreach ($address as $i) {
            if (is_string($i) && $i != 'vperror@vivid-planet.com') {
                $mail->addCc($i);
            }
        }
        $mail->send();
    }

    function handleError($code, $string, $file, $line)
    {
        // Fehler durch @ unterdrückt
        if (error_reporting() == 0) return;

        // Fehlertyp rausfinden
        switch ($code) {
            case E_ERROR:
            case E_USER_ERROR:
                $type = 'Error';
                break;
            case E_WARNING:
            case E_USER_WARNING:
                $type = 'Warning';
                break;
            case E_NOTICE:
            case E_USER_NOTICE:
                $type = 'Notice';
                break;
            default:
                $type = 'Unknown Error';
                break;
        }

        // CustomException erstellen
        $exception = new Vps_CustomException($string, $code);
        $exception->setLine($line);
        $exception->setFile($file);
        $exception->setType($type);

        // CustumException im Produktionsbetrieb nicht werfen, sondern Mail senden
        $address = Zend_Registry::get('config')->debug->errormail;
        if ($address != '' && ($code == E_NOTICE || $code == E_USER_NOTICE)) {
            Vps_Debug::sendErrorMail($exception, $address);
            return;
        } else {
            throw $exception;
        }
    }
}
