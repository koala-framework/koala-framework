<?php
class Vps_Auth_PartnerNetTestRequest extends Zend_Controller_Request_Http
{
    public $params = array();
    public $requestUri = array();
    public $host = 'www.example.com';
    public function getParam($param)
    {
        parse_str(parse_url('http://www.example.com'.$this->requestUri, PHP_URL_QUERY), $params);
        if (isset($params[$param])) return $params[$param];
        return null;
    }
    public function getHttpHost() { return $this->host; }
    public function getScheme() { return 'http'; }
    public function getRequestUri() { return $this->requestUri; }
}

/**
 * @group Vps_Auth
 */
class Vps_Auth_PartnerNetTest extends Vps_Test_TestCase
{
    public function testSuccessfulLogin()
    {
        $request  = new Vps_Auth_PartnerNetTestRequest();
        $url = "/partner-net-login?type=test123&BETR=99999";
        $url .= "&SYS1=".time();
        $url .= "&SYS3=".md5('http://www.example.com'.$url."&SECRET=example");
        $request->requestUri = $url;

        $adapter = new Vps_Auth_Adapter_PartnerNet();
        $adapter->setSecret('example');
        $adapter->setRequest($request);
        $r = $adapter->authenticate();
        $this->assertTrue($r->isValid());
    }

    public function testInvalidHash()
    {
        $request  = new Vps_Auth_PartnerNetTestRequest();
        $url = "/partner-net-login?type=test123&BETR=99999";
        $url .= "&SYS1=".time();
        $url .= "&SYS3=".md5('http://www.example.com'.$url."&SECRET=invalid");
        $request->requestUri = $url;

        $adapter = new Vps_Auth_Adapter_PartnerNet();
        $adapter->setSecret('example');
        $adapter->setRequest($request);
        $r = $adapter->authenticate();
        $this->assertFalse($r->isValid());
    }

    public function testTimedOut()
    {
        $request  = new Vps_Auth_PartnerNetTestRequest();
        $url = "/partner-net-login?type=test123&BETR=99999";
        $url .= "&SYS1=".time()-11*60;
        $url .= "&SYS3=".md5('http://www.example.com'.$url."&SECRET=example");
        $request->requestUri = $url;

        $adapter = new Vps_Auth_Adapter_PartnerNet();
        $adapter->setSecret('example');
        $adapter->setRequest($request);
        $r = $adapter->authenticate();
        $this->assertFalse($r->isValid());
    }

    public function testRealUrl()
    {
    
        $request = new Vps_Auth_PartnerNetTestRequest();
        $request->host = 'specials.audi.at';
        $url = "/partner-net-login?type=a8preview&HDLNR=undefined&PG_OS=1257837956517&SYS3=ab6671c5ddb4a46412ce3d68e7a9b955";
        $request->requestUri = $url;

        $adapter = new Vps_Auth_Adapter_PartnerNet();
        $adapter->setSecret('a8preview');
        $adapter->setMaxLinkAge(100000000000);
        $adapter->setRequest($request);
        $r = $adapter->authenticate();
        $this->assertTrue($r->isValid());
    }
}
