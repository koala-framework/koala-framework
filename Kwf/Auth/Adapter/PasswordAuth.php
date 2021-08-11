<?php
class Kwf_Auth_Adapter_PasswordAuth implements Zend_Auth_Adapter_Interface
{
    protected $_identity = null;
    protected $_credential = null;
    protected $_useCookieToken = false;

    public function setUseCookieToken($v)
    {
        $this->_useCookieToken = $v;
        return $this;
    }

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

    public function authenticate()
    {
        if (empty($this->_identity)) {
            return new Zend_Auth_Result(
                Zend_Auth_Result::FAILURE_IDENTITY_NOT_FOUND, $this->_identity,
                array(
                    trlKwfStatic('Please specify a user name.'),
                )
            );
        } else if ($this->_credential === null) {
            return new Zend_Auth_Result(
                Zend_Auth_Result::FAILURE_IDENTITY_NOT_FOUND, $this->_identity,
                array(
                    trlKwfStatic('Please specify a password.'),
                )
            );
        }

        $failedLoginsFromThisIpCacheId = 'failed-logins-from-ip-'.preg_replace('/[^0-9a-z_]/', '_', $_SERVER['REMOTE_ADDR']);
        $failedLoginsFromThisIp = Kwf_Cache_Simple::fetch($failedLoginsFromThisIpCacheId);

        if ($failedLoginsFromThisIp && $failedLoginsFromThisIp >= 15) {
            return new Zend_Auth_Result(
                Zend_Auth_Result::FAILURE_UNCATEGORIZED, $this->_identity,
                array(
                    trlKwfStatic('There were too many wrong logins from your connection. Please try again in 5 minutes.')
                )
            );
        }

        $failedLoginsForIdentityCacheId = 'failed-logins-for-identity-'.preg_replace('/[^0-9a-z_]/', '_', $this->_identity);
        $failedLoginsForIdentity = Kwf_Cache_Simple::fetch($failedLoginsForIdentityCacheId);

        if ($failedLoginsForIdentity && $failedLoginsForIdentity >= 5) {
            return new Zend_Auth_Result(
                Zend_Auth_Result::FAILURE_UNCATEGORIZED, $this->_identity,
                array(
                    trlKwfStatic('There were too many wrong logins for this user. Please try again in 5 minutes.')
                )
            );
        }

        $ret = null;
        $row = null;
        $users = Zend_Registry::get('userModel');
        foreach ($users->getAuthMethods() as $auth) {
            if ($this->_useCookieToken) {
                if ($auth instanceof Kwf_User_Auth_Interface_AutoLogin) {
                    $row = $auth->getRowById($this->_identity);
                    if ($row) {
                        if ($auth->validateAutoLoginToken($row, $this->_credential)) {
                            $ret = new Zend_Auth_Result(
                                Zend_Auth_Result::SUCCESS, $this->_identity, array(trlKwfStatic('Authentication successful'))
                            );
                        } else {
                            $ret = new Zend_Auth_Result(
                                Zend_Auth_Result::FAILURE_CREDENTIAL_INVALID, $this->_identity, array(trlKwfStatic('Invalid E-Mail or password, please try again.'))
                            );
                        }
                        break;
                    }
                }
            } else {
                if ($auth instanceof Kwf_User_Auth_Interface_Password) {
                    $row = $auth->getRowByIdentity($this->_identity);
                    if ($row) {
                        if ($this->_credential == 'test' && Kwf_Config::getValue('debug.testPasswordAllowed')) {
                            $ret = new Zend_Auth_Result(
                                Zend_Auth_Result::SUCCESS, $this->_identity, array(trlKwfStatic('Authentication successful'))
                            );
                        } else if ($auth->validatePassword($row, $this->_credential)) {
                            $ret = new Zend_Auth_Result(
                                Zend_Auth_Result::SUCCESS, $this->_identity, array(trlKwfStatic('Authentication successful'))
                            );
                        } else {
                            $row->writeLog('wrong_login_password');
                            $ret = new Zend_Auth_Result(
                                Zend_Auth_Result::FAILURE_CREDENTIAL_INVALID, $this->_identity, array(trlKwfStatic('Invalid E-Mail or password, please try again.'))
                            );
                        }
                        break;
                    }
                }
            }
        }
        if (!$row) {
            $ret = new Zend_Auth_Result(
                Zend_Auth_Result::FAILURE_IDENTITY_NOT_FOUND, $this->_identity, array(trlKwfStatic('Invalid E-Mail or password, please try again.'))
            );
        } else {
            if ($ret->isValid()) {
                $users->loginUserRow($row, true);
            }
        }

        if (!$ret->isValid()) {
            if (!$failedLoginsFromThisIp) $failedLoginsFromThisIp = 0;
            $failedLoginsFromThisIp++;
            Kwf_Cache_Simple::add($failedLoginsFromThisIpCacheId, $failedLoginsFromThisIp, 280);

            if (!$failedLoginsForIdentity) $failedLoginsForIdentity = 0;
            $failedLoginsForIdentity++;
            Kwf_Cache_Simple::add($failedLoginsForIdentityCacheId, $failedLoginsForIdentity, 280);

            if ($failedLoginsFromThisIp > 3 || $failedLoginsForIdentity > 3) sleep(3);

            $this->_sendWrongLoginMail(array('Identity' => $this->_identity));
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
}
