<?php
class Vps_Mail extends Zend_Mail
{
    // die folgenden 4 sind für maillog
    protected $_ownFrom = '';
    protected $_ownTo = array();
    protected $_ownCc = array();
    protected $_ownBcc = array();

    public function __construct($mustNotBeSet = null)
    {
        if ($mustNotBeSet) {
            throw new Vps_Exception("Vps_Mail got replaced with Vps_Mail_Template");
        }
        parent::__construct('utf-8');
    }

    public function addCc($email, $name='')
    {
        $this->_ownCc[] = trim("$name <$email>");
        if (Vps_Registry::get('config')->debug->sendAllMailsTo) {
            if ($name) {
                $this->addHeader('X-Real-Cc', $name ." <".$email.">");
            } else {
                $this->addHeader('X-Real-Cc', $email);
            }
        } else {
            parent::addCc($email, $name);
        }
        return $this;
    }

    public function addBcc($email)
    {
        $this->_ownBcc[] = $email;
        if (Vps_Registry::get('config')->debug->sendAllMailsTo) {
            $this->addHeader('X-Real-Bcc', $email);
        } else {
            parent::addBcc($email);
        }
        return $this;
    }

    public function addTo($email, $name='')
    {
        $this->_ownTo[] = trim("$name <$email>");
        if (Vps_Registry::get('config')->debug->sendAllMailsTo) {
            if ($name) {
                $this->addHeader('X-Real-Recipient', $name ." <".$email.">");
            } else {
                $this->addHeader('X-Real-Recipient', $email);
            }
        } else {
            parent::addTo($email, $name);
        }
        return $this;
    }

    public function setFrom($email, $name='')
    {
        if (empty($email)) {
            throw new Vps_Exception("Email address '$email' cannot be set as from part in a mail. Empty or invalid address.");
        }
        $this->_ownFrom = trim("$name <$email>");
        parent::setFrom($email, $name);
        return $this;
    }

    public function send($transport = null)
    {
        $mailSendAll = Vps_Registry::get('config')->debug->sendAllMailsTo;
        if ($mailSendAll) {
            parent::addTo($mailSendAll);
        }

        $mailSendAllBcc = Vps_Registry::get('config')->debug->sendAllMailsBcc;
        if ($mailSendAllBcc) {
            parent::addBcc($mailSendAllBcc);
        }

        if ($this->getFrom() == null) {
            $sender = $this->getSenderFromConfig();
            $this->setFrom($sender['address'], $sender['name']);
        }

        // in service mitloggen wenn url vorhanden
        if (Vps_Util_Model_MailLog::isAvailable()) {
            $r = Vps_Model_Abstract::getInstance('Vps_Util_Model_MailLog')->createRow();
            if (isset($_COOKIE['unitTest']) && $_COOKIE['unitTest']) {
                $r->identifier = $_COOKIE['unitTest'];
            }
            $r->from = $this->_ownFrom;
            $r->to = implode(';', $this->_ownTo);
            $r->cc = implode(';', $this->_ownCc);
            $r->bcc = implode(';', $this->_ownBcc);
            $r->subject = $this->getSubject();
            $r->body_text = $this->_bodyText;
            $r->body_html = $this->_bodyHtml;
            $r->save();
        }

        return parent::send($transport);
    }

    public static function getSenderFromConfig()
    {
        if (isset($_SERVER['HTTP_HOST'])) {
            $host = $_SERVER['HTTP_HOST'];
        } else {
            $host = Vps_Registry::get('config')->server->domain;
        }
        $hostNonWww = preg_replace('#^www\\.#', '', $host);
        return array(
            'address' => str_replace('%host%', $hostNonWww, Vps_Registry::get('config')->email->from->address),
            'name' => str_replace('%host%', $hostNonWww, Vps_Registry::get('config')->email->from->name)
        );
    }
}
