<?php
/*
F�r dieMD5 Verschl�sselung wird folgender String verwendet
http://www.audi.at/admin_specials/hdl_login.php?type=drivingdays06&HDLNR=01008&PG_OS=1146841194910&PZ=1137739280&USER=woi

wobei 'drivingdays' das Kennwort ist und in der URL NICHT mitgeschickt wird

Der MD5 Hash-Code der sich bei dieser URL ergibt ist:
SYS3=11dcca955f14bbf5016dffdf30cbb568
und wird an die URL angeh�ngt

PG_OS die Zeit in Millisekunden seit 01.01.1970 UTC ist (ein Zeitfenster
von +- 10 Minuten w�re angebracht) um ein kopieren und wiederverwenden des
Links zu unterbinden

Damit sie nachrechnen k�nnen, ob nun diese URL manuipuliert wurde oder
nicht, m�ssen Sie wie folgt vorgehen:

  den SYS3 Teil der URL ausschneiden und sich merken
  http://www.audi.at/admin_specials/hdl_login.php?type=drivingdays06&HDLNR=01008&PG_OS=1146841194910&PZ=1137739280&USER=woi&SYS3=11dcca955f14bbf5016dffdf30cbb568
  http://www.audi.at/admin_specials/hdl_login.php?type=drivingdays06&HDLNR=01008&PG_OS=1146841194910&PZ=1137739280&USER=woi
  http://www.audi.at/admin_specials/hdl_login.php?type=drivingdays06&HDLNR=01008&PG_OS=1146841194910&PZ=1137739280&USER=woi&SECRET=drivingdays
  eine MD5 Hash Berechnung �ber die neue URL machen und die beiden
  Hash-Werte miteinander vergleichen
  das war's

**/

class Vps_Auth_Adapter_PartnerNet implements Zend_Auth_Adapter_Interface
{
    protected $_request;
    protected $_secret;

    public function getSecret()
    {
        return $this->_secret;
    }

    public function setSecret($secret)
    {
        $this->_secret = $secret;
    }

    /**
     * Setter for the Request object
     *
     * @param  Zend_Controller_Request_Http $request
     * @return Zend_Auth_Adapter_Http Provides a fluent interface
     */
    public function setRequest(Zend_Controller_Request_Http $request)
    {
        $this->_request = $request;

        return $this;
    }

    /**
     * Getter for the Request object
     *
     * @return Zend_Controller_Request_Http
     */
    public function getRequest()
    {
        return $this->_request;
    }

    public function authenticate()
    {
        $r = $this->_request;

        $messages = array();
        $code = Zend_Auth_Result::FAILURE;

        $identity = $r->getParam('type');
        if ($r->getParam('PG_OS')) {
            $time = $r->getParam('PG_OS');
        } else if ($r->getParam('SYS1')) {
            $time = $r->getParam('SYS1');
        } else {
            $time = 0;
        }
        $hash = $r->getParam('SYS3');

        $diff = abs((time()) - $time);

        if ($diff < (60*10)) {

            $url = $r->getRequestUri();
            $url = preg_replace('#&SYS3=[a-f0-9]+#', '', $url);
            $url = $r->getScheme().'://'.$r->getHttpHost().$url;
            $url .= '&SECRET='.$this->_secret;

            if ($hash == md5($url)) {
                $_SESSION['partnerNet'] = $_GET;
                $code = Zend_Auth_Result::SUCCESS;
            } else {
                $code = Zend_Auth_Result::FAILURE_UNCATEGORIZED;
                $messages[] = 'Invalid hash';
            }
        } else {
            $code = Zend_Auth_Result::FAILURE_UNCATEGORIZED;
            $messages[] = 'Link timed out';
        }

        return new Zend_Auth_Result($code, $identity, $messages);
    }
}
