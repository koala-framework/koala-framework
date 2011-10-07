<?php
/**
 * @group Kwc_Trl
 * @group Kwc_Trl_StaticTexts
 */
class Kwc_Trl_StaticTexts_Test extends Kwc_TestAbstract
{
    public function setUp()
    {
        Kwf_Registry::get('config')->languages = array('de', 'en');
        Kwf_Trl::getInstance()->setWebCodeLanguage('de');
        Kwf_Trl::getInstance()->setModel(new Kwc_Trl_StaticTexts_TrlModelWeb(), Kwf_Trl::SOURCE_WEB);
        parent::setUp('Kwc_Trl_StaticTexts_Root');
    }

    public function tearDown()
    {
        Kwf_Trl::getInstance()->setWebCodeLanguage(null);
        Kwf_Trl::getInstance()->setModel(null, Kwf_Trl::SOURCE_WEB);
        parent::tearDown();
    }

    public function testDe()
    {

        $c = $this->_root->getPageByUrl('http://'.Kwf_Registry::get('config')->server->domain.'/de/test', 'de');
        $this->assertEquals('de', $c->getLanguage());
        $render = $c->render();
        $this->assertContains('trlTest: Sichtbar', $render);
        $this->assertContains('trlcTest: Am', $render);
        $this->assertContains('trlpTest1: Antwort', $render);
        $this->assertContains('trlpTest2: Antworten', $render);
        $this->assertContains('trlcpTest1: Antwort', $render);
        $this->assertContains('trlcpTest2: Antworten', $render);

        $this->assertContains('trlKwfTest: Sichtbar', $render);
        $this->assertContains('trlcKwfTest: Am', $render);
        $this->assertContains('trlpKwfTest1: Antwort', $render);
        $this->assertContains('trlpKwfTest2: Antworten', $render);
        $this->assertContains('trlcpKwfTest1: Antwort', $render);
        $this->assertContains('trlcpKwfTest2: Antworten', $render);

        $this->assertContains('trlTestTpl: Sichtbar', $render);
        $this->assertContains('trlcTestTpl: Am', $render);
        $this->assertContains('trlpTest1Tpl: Antwort', $render);
        $this->assertContains('trlpTest2Tpl: Antworten', $render);
        $this->assertContains('trlcpTest1Tpl: Antwort', $render);
        $this->assertContains('trlcpTest2Tpl: Antworten', $render);

        $this->assertContains('trlKwfTestTpl: Sichtbar', $render);
        $this->assertContains('trlcKwfTestTpl: Am', $render);
        $this->assertContains('trlpKwfTest1Tpl: Antwort', $render);
        $this->assertContains('trlpKwfTest2Tpl: Antworten', $render);
        $this->assertContains('trlcpKwfTest1Tpl: Antwort', $render);
        $this->assertContains('trlcpKwfTest2Tpl: Antworten', $render);
    }

    public function testEn()
    {
        $c = $this->_root->getPageByUrl('http://'.Kwf_Registry::get('config')->server->domain.'/en/test', 'en');
        $this->assertEquals('en', $c->getLanguage());
        $render = $c->render();
        $this->assertContains('trlTest: Visible', $render);
        $this->assertContains('trlcTest: On', $render);
        $this->assertContains('trlpTest1: reply', $render);
        $this->assertContains('trlpTest2: replies', $render);
        $this->assertContains('trlcpTest1: reply', $render);
        $this->assertContains('trlcpTest2: replies', $render);

        $this->assertContains('trlKwfTest: Visible', $render);
        $this->assertContains('trlcKwfTest: On', $render);
        $this->assertContains('trlpKwfTest1: reply', $render);
        $this->assertContains('trlpKwfTest2: replies', $render);
        $this->assertContains('trlcpKwfTest1: reply', $render);
        $this->assertContains('trlcpKwfTest2: replies', $render);

        $this->assertContains('trlTestTpl: Visible', $render);
        $this->assertContains('trlcTestTpl: On', $render);
        $this->assertContains('trlpTest1Tpl: reply', $render);
        $this->assertContains('trlpTest2Tpl: replies', $render);
        $this->assertContains('trlcpTest1Tpl: reply', $render);
        $this->assertContains('trlcpTest2Tpl: replies', $render);

        $this->assertContains('trlKwfTestTpl: Visible', $render);
        $this->assertContains('trlcKwfTestTpl: On', $render);
        $this->assertContains('trlpKwfTest1Tpl: reply', $render);
        $this->assertContains('trlpKwfTest2Tpl: replies', $render);
        $this->assertContains('trlcpKwfTest1Tpl: reply', $render);
        $this->assertContains('trlcpKwfTest2Tpl: replies', $render);

    }
}
