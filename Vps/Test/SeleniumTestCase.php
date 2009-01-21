<?php
class Vps_Test_SeleniumTestCase extends PHPUnit_Extensions_SeleniumTestCase
{
    protected $autoStop = false;
    protected $_unitTestCookie;
    protected $_domain = null;

    public static function suite($className)
    {
        self::$browsers = array();
        foreach (Vps_Registry::get('config')->server->testBrowser as $b) {
            if (!$b->browser) continue; //deaktiviert
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
        if (!$cfg = Vps_Registry::get('testServerConfig')) {
            throw new Vps_Exception("testServerConfig not set");
        }
        $d = $this->_domain;
        if (!$d) {
            $domain = $cfg->server->domain;
        } else {
            if (!isset($cfg->vpc->domains->$d)) {
                throw new Vps_Exception("Domain '$d' not found in config");
            }
            $domain = $cfg->vpc->domains->$d->domain;
        }
        $this->setBrowserUrl('http://'.$domain.'/');

        $this->_unitTestCookie = md5(uniqid('testId', true));
    }

    protected function assertPostConditions()
    {
        try {
            $this->stop();
        } catch (RuntimeException $e) {}
    }

    protected function tearDown()
    {
        if (Zend_Registry::get('config')->server->autoStopTest) {
            try {
                $this->stop();
            } catch (RuntimeException $e) {}
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

    public function __call($command, $arguments)
    {
        if ($command == 'open') {
            $this->deleteCookie('unitTest', 'path=/');
            $this->createCookie('unitTest='.$this->_unitTestCookie, 'path=/');
        }
        $ret = parent::__call($command, $arguments);
        return $ret;
    }

    protected function defaultAssertions($command)
    {
        if ($command == 'waitForPageToLoad' || $command == 'open') {
            if ($this->isElementPresent('css=#exception')) {
                $exception = $this->getText('css=#exception');
                $exception = unserialize(base64_decode($exception));
                throw $exception;
            }
            $this->assertTextNotPresent('Seite wurde nicht gefunden');
            $this->assertTextNotPresent('was not found on this server');
            $this->assertTitleNotContains('Internal Server Error');
            $this->assertTextNotPresent('Exception');
            $this->assertTextNotPresent('Fatal error');
            $this->assertTextNotPresent('Parse error');
            $this->assertTextNotPresent('warning');
            $this->assertTextNotPresent('notice');
        }
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

    protected function _getLatestMail($select = null)
    {
        $m = Vps_Model_Abstract::getInstance('Vps_Util_Model_MailLog');
        if (!$select) $select = $m->select();
        $select->order('id', 'DESC');
        $select->limit(1);
        return $this->_getMails($select)->current();
    }

    protected function _getMails($select = null)
    {
        $m = Vps_Model_Abstract::getInstance('Vps_Util_Model_MailLog');
        if (!$select) $select = $m->select();
        return $m->getRows($select
                    ->whereEquals('identifier', $this->_unitTestCookie));
    }

    public function assertBodyText($search)
    {
        $this->assertEquals($search, $this->getText('//body'));
    }

    public function assertTitleContains($title, $message = '')
    {
        $this->assertContains($title, $this->getTitle(), $message);
    }

    public function assertTitleNotContains($title, $message = '')
    {
        $this->assertNotContains($title, $this->getTitle(), $message);
    }
}
