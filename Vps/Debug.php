<?php
class Vps_Debug
{
    static $_enabled = true;
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
        $body .= "\n";
        if (isset($_SERVER['REQUEST_URI'])) {
            $body .= "\nREQUEST_URI: ".$_SERVER['REQUEST_URI'];
        }
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
        $body .= "\n\n------------------\n\n_FILES:\n";
        $body .= print_r($_FILES, true);
        $body .= "\n\n------------------\n\n_SESSION:\n";
        $body .= print_r($_SESSION, true);
        $body = substr($body, 0, 5000);
        $mail = new Zend_Mail('utf-8');
        $subject = $_SERVER['HTTP_HOST'] . ': ' . $type;
        if (isset($_SERVER['REQUEST_URI'])) {
            $subject .= ' - '.$_SERVER['REQUEST_URI'];
        }
        $mail->setBodyText($body)
            ->setSubject($subject);
        $mail->addTo('vperror@vivid-planet.com');
        if (is_string($address)) $address = array($address);
        foreach ($address as $i) {
            if (is_string($i) && $i != 'vperror@vivid-planet.com') {
                $mail->addCc($i);
            }
        }
        $mail->send();
    }

    public static function handleError($errno, $errstr, $errfile, $errline)
    {
        if (error_reporting() == 0) return; // error unterdrückt mit @foo()
        $exception = new ErrorException($errstr, 0, $errno, $errfile, $errline);

        // CustumException im Produktionsbetrieb nicht werfen, sondern Mail senden
        $address = Zend_Registry::get('config')->debug->errormail;
        if (!isset($_SERVER['SHELL']) && $address != '' && ($errno == E_NOTICE || $errno == E_USER_NOTICE)) {
            Vps_Debug::sendErrorMail($exception, $address);
            return;
        } else {
            throw $exception;
        }
    }

    public static function enable()
    {
        self::$_enabled = true;
    }

    public static function disable()
    {
        p('debug output disabled');
        self::$_enabled = false;
    }

    public static function isEnabled()
    {
        return self::$_enabled;
    }
}
