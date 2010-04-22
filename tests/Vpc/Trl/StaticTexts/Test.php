<?php
/**
 * @group Vpc_Trl
 * @group Vpc_Trl_StaticTexts
 */
class Vpc_Trl_StaticTexts_Test extends Vpc_TestAbstract
{
    public function setUp()
    {
        Vps_Registry::get('config')->languages = array('de', 'en');
        Vps_Registry::get('trl')->setWebCodeLanguage('de');
        Vps_Registry::get('trl')->setModel(new Vpc_Trl_StaticTexts_TrlModelWeb(), Vps_Trl::SOURCE_WEB);
        parent::setUp('Vpc_Trl_StaticTexts_Root');
    }

    public function tearDown()
    {
        Vps_Registry::get('trl')->setWebCodeLanguage(null);
        Vps_Registry::get('trl')->setModel(null, Vps_Trl::SOURCE_WEB);
        parent::tearDown();
    }

    public function testDe()
    {

        $c = $this->_root->getPageByUrl('http://'.Vps_Registry::get('config')->server->domain.'/de/test', 'de');
        $this->assertEquals('de', $c->getLanguage());
        $render = $c->render();
        $this->assertContains('trlTest: Sichtbar', $render);
        $this->assertContains('trlcTest: Am', $render);
        $this->assertContains('trlpTest1: Antwort', $render);
        $this->assertContains('trlpTest2: Antworten', $render);
        $this->assertContains('trlcpTest1: Antwort', $render);
        $this->assertContains('trlcpTest2: Antworten', $render);

        $this->assertContains('trlVpsTest: Sichtbar', $render);
        $this->assertContains('trlcVpsTest: Am', $render);
        $this->assertContains('trlpVpsTest1: Antwort', $render);
        $this->assertContains('trlpVpsTest2: Antworten', $render);
        $this->assertContains('trlcpVpsTest1: Antwort', $render);
        $this->assertContains('trlcpVpsTest2: Antworten', $render);

        $this->assertContains('trlTestTpl: Sichtbar', $render);
        $this->assertContains('trlcTestTpl: Am', $render);
        $this->assertContains('trlpTest1Tpl: Antwort', $render);
        $this->assertContains('trlpTest2Tpl: Antworten', $render);
        $this->assertContains('trlcpTest1Tpl: Antwort', $render);
        $this->assertContains('trlcpTest2Tpl: Antworten', $render);

        $this->assertContains('trlVpsTestTpl: Sichtbar', $render);
        $this->assertContains('trlcVpsTestTpl: Am', $render);
        $this->assertContains('trlpVpsTest1Tpl: Antwort', $render);
        $this->assertContains('trlpVpsTest2Tpl: Antworten', $render);
        $this->assertContains('trlcpVpsTest1Tpl: Antwort', $render);
        $this->assertContains('trlcpVpsTest2Tpl: Antworten', $render);
    }

    public function testEn()
    {
        $c = $this->_root->getPageByUrl('http://'.Vps_Registry::get('config')->server->domain.'/en/test', 'en');
        $this->assertEquals('en', $c->getLanguage());
        $render = $c->render();
        $this->assertContains('trlTest: Visible', $render);
        $this->assertContains('trlcTest: On', $render);
        $this->assertContains('trlpTest1: reply', $render);
        $this->assertContains('trlpTest2: replies', $render);
        $this->assertContains('trlcpTest1: reply', $render);
        $this->assertContains('trlcpTest2: replies', $render);

        $this->assertContains('trlVpsTest: Visible', $render);
        $this->assertContains('trlcVpsTest: On', $render);
        $this->assertContains('trlpVpsTest1: reply', $render);
        $this->assertContains('trlpVpsTest2: replies', $render);
        $this->assertContains('trlcpVpsTest1: reply', $render);
        $this->assertContains('trlcpVpsTest2: replies', $render);

        $this->assertContains('trlTestTpl: Visible', $render);
        $this->assertContains('trlcTestTpl: On', $render);
        $this->assertContains('trlpTest1Tpl: reply', $render);
        $this->assertContains('trlpTest2Tpl: replies', $render);
        $this->assertContains('trlcpTest1Tpl: reply', $render);
        $this->assertContains('trlcpTest2Tpl: replies', $render);

        $this->assertContains('trlVpsTestTpl: Visible', $render);
        $this->assertContains('trlcVpsTestTpl: On', $render);
        $this->assertContains('trlpVpsTest1Tpl: reply', $render);
        $this->assertContains('trlpVpsTest2Tpl: replies', $render);
        $this->assertContains('trlcpVpsTest1Tpl: reply', $render);
        $this->assertContains('trlcpVpsTest2Tpl: replies', $render);

    }
}
