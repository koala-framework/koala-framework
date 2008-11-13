<?php
class Vps_Test_SeleniumTestCase extends PHPUnit_Extensions_SeleniumTestCase
{
    protected $autoStop = false;

    public static function suite($className)
    {
        self::$browsers = array();
        foreach (Vps_Registry::get('config')->server->testBrowser as $b) {
            $b = $b->toArray();
            $b['port'] = (int)$b['port'];
            $b['timeout'] = (int)$b['timeout'];
            self::$browsers[] = $b;
        }
        if (!self::$browsers) {
            throw new Vps_Exception("No test-Browser avaliable");
        }
        return parent::suite($className);
    }

    protected function setUp()
    {
        $domain = Vps_Registry::get('testDomain');
        if (!$domain) {
            throw new Vps_Exception("No testDomain set");
        }
        $this->setBrowserUrl('http://'.$domain.'/');
    }

    protected function tearDown()
    {
        try {
            $this->stop();
        }
        catch (RuntimeException $e) {
        }
    }

    public function clickAndWait($link)
    {
        $this->click($link);
        $this->waitForPageToLoad("30000");
    }
    public function sessionRestart()
    {
        $this->open("/vps/debug/session-restart");
    }
    public function assertContainsText($locator, $text)
    {
        if (is_array($text)) {
            foreach ($text as $k=>$i) {
                $this->assertContainsText($locator.'['.$k.']', $i);
            }
        } else {
            $this->assertTrue((bool)preg_match('#'.$text.'#', $this->getText($locator)));
        }
    }

    protected function _reloadSession()
    {
        $data = file_get_contents(session_save_path().'/sess_'.Zend_Session::getId());
        $vars = preg_split('/([a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff^|]*)\|/',
                $data, -1, PREG_SPLIT_NO_EMPTY | PREG_SPLIT_DELIM_CAPTURE);
        $data = array();
        for($i=0; isset($vars[$i]); $i++) {
            $data[$vars[$i++]]
                = unserialize($vars[$i]);
        }
        $_SESSION = $data;
    }

    public function __call($command, $arguments)
    {
        if ($command == 'open') {
            $this->deleteCookie(session_name(), '/');
            $this->createCookie(session_name().'='.session_id(), 'path=/');
            $this->createCookie('unitTest=1', 'path=/');
            Zend_Session::writeClose();
        }
        return parent::__call($command, $arguments);
    }

    public function openVpc($url)
    {
        return $this->open('/vps/vpctest/'.Vps_Component_Data_Root::getComponentClass().$url);
    }
    public function openVpcEdit($componentClass, $componentId)
    {
        $url = '/vps/componentedittest/'.
                Vps_Component_Data_Root::getComponentClass().'/'.
                $componentClass.
                '?componentId='.$componentId;
        return $this->open($url);
    }

    protected function defaultAssertions($action)
    {
        if ($action == 'waitForPageToLoad') {
            if ($this->isElementPresent('id=exception')) {
                $exception = $this->getText('id=exception');
                $exception = unserialize(base64_decode($exception));
                throw $exception;
            }
            $this->assertTextNotPresent('Exception');
            $this->assertTextNotPresent('Fatal error');
            $this->assertTextNotPresent('warning');
            $this->assertTextNotPresent('notice');
        }
    }

    protected function waitForConnections()
    {
        $this->waitForCondition('selenium.browserbot.getCurrentWindow().Vps.Connection.runningRequests==0');
    }

}
