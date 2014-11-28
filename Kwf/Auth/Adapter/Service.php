<?php

require_once 'Zend/Auth/Adapter/Interface.php';

class Kwf_Auth_Adapter_Service implements Zend_Auth_Adapter_Interface
{
    protected $_identity = null;
    protected $_credential = null;

    protected $_userId = null;

    public function setIdentity($identd)
    {
        $this->_identity = $identd;
        return $this;
    }

    public function setCredential($credential)
    {
        $this->_credential = $credential;
        return $this;
    }

    public function getUserId()
    {
        return $this->_userId;
    }

    public function authenticate()
    {
        if (empty($this->_identity)) {
            return new Zend_Auth_Result(
                Zend_Auth_Result::FAILURE_IDENTITY_NOT_FOUND, $this->_identity,
                array(
                    trlKwf('Please specify a user name.'),
                )
            );
        } else if ($this->_credential === null) {
            return new Zend_Auth_Result(
                Zend_Auth_Result::FAILURE_IDENTITY_NOT_FOUND, $this->_identity,
                array(
                    trlKwf('Please specify a password.'),
                )
            );
        }

        $cache = $this->_getCache();
        $failedLoginsFromThisIp = $cache->load($this->_getCacheId());
        if ($failedLoginsFromThisIp && $failedLoginsFromThisIp >= 15) {
            return new Zend_Auth_Result(
                Zend_Auth_Result::FAILURE_UNCATEGORIZED, $this->_identity,
                array(
                    trlKwf('Too many wrong logins.'),
                    trlKwf('There were too many wrong logins from your connection. Please try again in 5 minutes.')
                )
            );
        }

        $users = Zend_Registry::get('userModel')->getKwfModel();
        $result = $users->login($this->_identity, $this->_credential);

        if (isset($result['userId'])) {
            $this->_userId = $result['userId'];
        }

        $ret = new Zend_Auth_Result(
            $result['zendAuthResultCode'], $result['identity'], $result['messages']
        );

        if (!$ret->isValid()) {
            $cache = $this->_getCache();
            $failedLoginsFromThisIp = $cache->load($this->_getCacheId());
            if (!$failedLoginsFromThisIp) $failedLoginsFromThisIp = 0;
            $failedLoginsFromThisIp++;

            $cache->save($failedLoginsFromThisIp, $this->_getCacheId());
            $this->_sendWrongLoginMail(array('Identity' => $this->_identity));
            if ($failedLoginsFromThisIp > 3) sleep(3);
        }

        return $ret;
    }

    private function _sendWrongLoginMail(array $vars)
    {
        $to = Kwf_Registry::get('config')->debug->sendWrongLoginsTo;
        if (!$to) return;

        $body = "\n";
        if (isset($_SERVER['REQUEST_URI'])) {
            $body .= "\nREQUEST_URI: ".$_SERVER['REQUEST_URI'];
        }
        $body .= "\nHTTP_REFERER: ".(isset($_SERVER['HTTP_REFERER'])
                                        ? $_SERVER['HTTP_REFERER'] : '(none)');
        $body .= "\n";
        foreach ($vars as $k => $v) {
            $body .= "\n$k: $v";
        }
        $emailPostVars = $_POST;
        foreach ($emailPostVars as $k => $epv) {
            if (strpos($k, 'pass') !== false) $emailPostVars[$k] = '--- hidden ---';
        }
        $body .= "\n\n------------------\n\n_GET:\n";
        $body .= print_r($_GET, true);
        $body .= "\n\n------------------\n\n_POST:\n";
        $body .= print_r($emailPostVars, true);
        $body .= "\n\n------------------\n\n_SERVER:\n";
        $body .= print_r($_SERVER, true);
        $body .= "\n\n------------------\n\n_FILES:\n";
        $body .= print_r($_FILES, true);
        $body .= "\n\n------------------\n\n_SESSION:\n";
        $body .= print_r($_SESSION, true);
        $body = substr($body, 0, 5000);

        $subject = 'Wrong Login: ';
        $subject .= isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : '';
        $requestUri = isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : '' ;
        if ($requestUri) $subject .= ' - '.$requestUri;

        $mail = new Zend_Mail('utf-8');
        $mail->setBodyText($body)
            ->setSubject($subject);
        $mail->addTo($to);
        $mail->send();

    }

    private function _getCacheId()
    {
        return str_replace(array('.', ':', ',', ' '), '_', $_SERVER['REMOTE_ADDR']);
    }

    private function _getCache()
    {
        return Kwf_Cache::factory('Core', 'File',
            array(
                'lifetime' => 280,
                'automatic_serialization'=>true
            ),
            array(
                'cache_dir' => 'cache/config',
                'file_name_prefix' => 'login_brute_force_'
            )
        );
    }

}
