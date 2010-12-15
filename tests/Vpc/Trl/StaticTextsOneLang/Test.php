<?php
/**
 * @group Vpc_Trl
 * @group Vpc_Trl_StaticTexts
 * @group Vpc_Trl_StaticTexts_OneLang
 */
class Vpc_Trl_StaticTextsOneLang_Test extends Vpc_TestAbstract
{
    public function setUp()
    {
        Vps_Registry::get('config')->languages = array('de', 'en');
        Vps_Trl::getInstance()->setWebCodeLanguage('de');
        Vps_Trl::getInstance()->setModel(new Vpc_Trl_StaticTextsOneLang_TrlModelWeb(), Vps_Trl::SOURCE_WEB);
        parent::setUp('Vpc_Trl_StaticTextsOneLang_Root');
    }

    public function tearDown()
    {
        Vps_Trl::getInstance()->setWebCodeLanguage(null);
        Vps_Trl::getInstance()->setModel(null, Vps_Trl::SOURCE_WEB);
        parent::tearDown();
    }

    public function testOneLang()
    {
        $c = $this->_root->getPageByUrl('http://'.Vps_Registry::get('config')->server->domain.'/trltest', 'de');

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
}
