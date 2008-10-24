<?php
class Vps_Test_SeleniumTestCase extends PHPUnit_Extensions_SeleniumTestCase
{
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
}
