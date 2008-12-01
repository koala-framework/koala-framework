<?php
class Vps_Exception extends Vps_Exception_NoMail
{
    private $_mail;

    public function setMail(Zend_Mail $mail)
    {
        $this->_mail = $mail;
    }

    public function getMail()
    {
        if (!$this->_mail) $this->_mail = new Zend_Mail('utf-8');
        return $this->_mail;
    }

    public function sendErrorMail()
    {
        $requestUri = isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : '' ;
        $address = Zend_Registry::get('config')->debug->errormail;

        if (!self::isDebug()
            && $address
            && substr($requestUri, -12) != '/favicon.ico'
            && substr($requestUri, -10) != '/robots.txt')
        {
            $type = get_class($this->getException());
            $body = $this->getException()->__toString();
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
            $subject = isset($_SERVER['HTTP_HOST']) ?
                $_SERVER['HTTP_HOST'] . ': ' . $type : $type;
            if ($requestUri) $subject .= ' - '.$requestUri;

            $mail = $this->getMail();
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
            return true;
        }
        return false;
    }

}
