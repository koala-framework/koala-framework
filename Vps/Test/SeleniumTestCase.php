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

    protected function assertPostConditions()
    {
        try {
            $this->stop();
        }
        catch (RuntimeException $e) {
        }
    }

    protected function tearDown()
    {
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
        $ret = parent::__call($command, $arguments);
        if ($command == 'open') {
            $this->waitForPageToLoad();
        }
        if ($command == 'waitForPageToLoad') {
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
        
        return $ret;
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

    protected function waitForConnections()
    {
        $this->waitForCondition('selenium.browserbot.getCurrentWindow().Vps.Connection.runningRequests==0');
    }
    
    //kopiert von PhpUnit, nur um eigenen Driver verwenden zu können
    protected function getDriver(array $browser)
    {
        if (isset($browser['name'])) {
            if (!is_string($browser['name'])) {
                throw new InvalidArgumentException;
            }
        } else {
            $browser['name'] = '';
        }

        if (isset($browser['browser'])) {
            if (!is_string($browser['browser'])) {
                throw new InvalidArgumentException;
            }
        } else {
            $browser['browser'] = '';
        }

        if (isset($browser['host'])) {
            if (!is_string($browser['host'])) {
                throw new InvalidArgumentException;
            }
        } else {
            $browser['host'] = 'localhost';
        }

        if (isset($browser['port'])) {
            if (!is_int($browser['port'])) {
                throw new InvalidArgumentException;
            }
        } else {
            $browser['port'] = 4444;
        }

        if (isset($browser['timeout'])) {
            if (!is_int($browser['timeout'])) {
                throw new InvalidArgumentException;
            }
        } else {
            $browser['timeout'] = 30000;
        }

        $driver = new Vps_Test_SeleniumTestCase_Driver;
        $driver->setName($browser['name']);
        $driver->setBrowser($browser['browser']);
        $driver->setHost($browser['host']);
        $driver->setPort($browser['port']);
        $driver->setTimeout($browser['timeout']);
        $driver->setTestCase($this);
        $driver->setTestId($this->testId);

        $this->drivers[] = $driver;

        return $driver;
    }
}
