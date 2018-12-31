<?php
class Kwf_Mail extends Zend_Mail
{
    protected $_attachImages = false;
    protected $_domain = null;

    public function __construct($mustNotBeSet = null)
    {
        if ($mustNotBeSet) {
            throw new Kwf_Exception("Kwf_Mail got replaced with Kwf_Mail_Template");
        }
        parent::__construct('utf-8');
    }

    public function getMailContent($type = Kwf_Model_Mail_Row::MAIL_CONTENT_AUTO)
    {
        if ($type == Kwf_Model_Mail_Row::MAIL_CONTENT_AUTO) {
            $ret = $this->getBodyHtml(true);
            if (!$ret) $ret = $this->getBodyText(true);
            return $ret;
        } else if ($type == Kwf_Model_Mail_Row::MAIL_CONTENT_HTML) {
            return $this->getBodyHtml(true);
        } else if ($type == Kwf_Model_Mail_Row::MAIL_CONTENT_TEXT) {
            return $this->getBodyText(true);
        }
        return null;
    }

    public function addCc($email, $name='')
    {
        if (Kwf_Registry::get('config')->debug->sendAllMailsTo) {
            $this->_addDebugAddressHeader('X-Real-Cc', $email, $name);
        } else {
            parent::addCc($email, $name);
        }
        return $this;
    }

    public function addBcc($email)
    {
        if (Kwf_Registry::get('config')->debug->sendAllMailsTo) {
            $this->_addDebugAddressHeader('X-Real-Bcc', $email);
        } else {
            parent::addBcc($email);
        }
        return $this;
    }

    public function addTo($email, $name='')
    {
        if (Kwf_Registry::get('config')->debug->sendAllMailsTo) {
            $this->_addDebugAddressHeader('X-Real-Recipient', $email);
        } else {
            parent::addTo($email, $name);
        }
        return $this;
    }

    private function _addDebugAddressHeader($headerName, $email, $name = '')
    {
        if (!is_array($email)) {
            $email = array($name => $email);
        }
        foreach ($email as $n => $recipient) {
            if ($recipient) {
                if ($n && !is_int($n)) {
                    $header = $n ." <".$recipient.">";
                } else {
                    $header = $recipient;
                }
                $this->addHeader($headerName, $header);
            }
        }
    }

    public function setAttachImages($attachImages)
    {
        $this->_attachImages = $attachImages;
        return $this;
    }

    public function setDomain($domain)
    {
        $this->_domain = $domain;
        return $this;
    }

    public function getDomain()
    {
        if (!$this->_domain) {
            $this->_domain = Kwf_Config::getValue('server.domain');
        }
        return $this->_domain;
    }

    public function setBodyHtml($html, $charset = null, $encoding = Zend_Mime::ENCODING_QUOTEDPRINTABLE)
    {
        while (preg_match('/(img src|background)=\"\/(.*?)\"/i', $html, $matches)) {
            $path = '/' . $matches[2];
            if ($this->_attachImages) {
                if (substr($path, 0, 6) == '/media') {
                    $parts = explode('/', substr($path, 1));
                    $class = $parts[1];
                    $id = $parts[2];
                    $type = $parts[3];
                    $checksum = $parts[4];
                    $filename = $parts[6];
                    $output = Kwf_Media::getOutputWithoutCheckingIsValid($class, $id, $type);
                } else {
                    try {
                        $f = new Kwf_Assets_Loader();
                        $output = $f->getFileContents(substr($path, 8));
                    } catch (Kwf_Exception_NotFound $e) {
                        throw new Kwf_Exception('Asset not found: ' . $path);
                    }
                }
                if (isset($output['contents'])) {
                    $contents = $output['contents'];
                } else if (isset($output['file'])) {
                    $contents = file_get_contents($output['file']);
                } else {
                    throw new Kwf_Exception("didn't get image contents");
                }
                $image = new Zend_Mime_Part($contents);
                $image->type = $output['mimeType'];
                $image->disposition = Zend_Mime::DISPOSITION_INLINE;
                $image->encoding = Zend_Mime::ENCODING_BASE64;
                $filename = rawurldecode(substr(strrchr($path, '/'), 1));
                $filename = preg_replace('/([^a-z0-9\-\.]+)/i', '_', $filename);
                $image->filename = $filename;
                $image->id = md5($path);
                $this->setType(Zend_Mime::MULTIPART_RELATED);
                $this->addAttachment($image);
                $replace = "cid:{$image->id}";
            } else {
                $replace = "http://" . $this->getDomain() . $path;
            }
            $html = str_replace($matches[0], "{$matches[1]}=\"$replace\"", $html);
        }
        parent::setBodyHtml($html, $charset, $encoding);
    }

    public function setFrom($email, $name='')
    {
        if (empty($email)) {
            throw new Kwf_Exception("Email address '$email' cannot be set as from part in a mail. Empty or invalid address.");
        }
        parent::clearFrom();
        parent::setFrom($email, $name);
        return $this;
    }

    public function setReturnPath($email)
    {
        parent::clearReturnPath();
        return parent::setReturnPath($email);
    }

    public function setReplyTo($email, $name = null)
    {
        parent::clearReplyTo();
        return parent::setReplyTo($email, $name);
    }

    public function send($transport = null)
    {
        $mailSendAll = Kwf_Config::getValueArray('debug.sendAllMailsTo');
        if ($mailSendAll) {
            parent::addTo($mailSendAll);
        }

        $mailSendAllBcc = Kwf_Config::getValueArray('debug.sendAllMailsBcc');
        if ($mailSendAllBcc) {
            parent::addBcc($mailSendAllBcc);
        }

        if ($this->getFrom() == null) {
            $sender = $this->getSenderFromConfig();
            $this->setFrom($sender['address'], $sender['name']);
        }

        if (!$transport) {

            static $smtpTransport;
            if (isset($smtpTransport)) {
                $transport = $smtpTransport; //reuse previously created transport to reuse connection
            } else if (Kwf_Config::getValue('email.transport')) {
                $transport = Kwf_Config::getValue('email.transport');
                $transport = new $transport();
                $smtpTransport = $transport;
            } else if (Kwf_Config::getValue('email.smtp.host')) {
                $smtpTransport = new Zend_Mail_Transport_Smtp(
                    Kwf_Config::getValue('email.smtp.host'),
                    array(
                        'auth' => Kwf_Config::getValue('email.smtp.auth'),
                        'username' => Kwf_Config::getValue('email.smtp.username'),
                        'password' => Kwf_Config::getValue('email.smtp.password'),
                        'ssl' => Kwf_Config::getValue('email.smtp.ssl'),
                        'port' => Kwf_Config::getValue('email.smtp.port'),
                    )
                );
                $transport = $smtpTransport;
            } else {
                if ($this->getReturnPath()) {
                    $transport = new Zend_Mail_Transport_Sendmail('-f ' . $this->getReturnPath());
                } else {
                    // default transport
                }
            }
        }

        return parent::send($transport);
    }

    public static function getSenderFromConfig()
    {
        return array(
            'address' => self::replaceHost(Kwf_Registry::get('config')->email->from->address),
            'name' => self::replaceHost(Kwf_Registry::get('config')->email->from->name)
        );
    }

    public static function replaceHost($string)
    {
        if (isset($_SERVER['HTTP_HOST'])) {
            $host = $_SERVER['HTTP_HOST'];
        } else {
            $host = Kwf_Registry::get('config')->server->domain;
        }
        $hostNonWww = preg_replace('#^www\\.#', '', $host);
        return str_replace('%host%', $hostNonWww, $string);
    }
}
