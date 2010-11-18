<?php
/**
 * @group Vpc_Trl
 * @group Vpc_Trl_StaticTexts
 * @group Vpc_Trl_StaticTexts_Placeholder
 */
class Vpc_Trl_StaticTextsPlaceholder_Test extends Vpc_TestAbstract
{
    public function setUp()
    {
        Vps_Registry::get('config')->languages = array('de', 'en');
        Vps_Registry::get('trl')->setWebCodeLanguage('de');
        Vps_Registry::get('trl')->setModel(new Vpc_Trl_StaticTextsPlaceholder_TrlModelWeb(), Vps_Trl::SOURCE_WEB);
        parent::setUp('Vpc_Trl_StaticTextsPlaceholder_Root');
    }

    public function tearDown()
    {
        Vps_Registry::get('trl')->setWebCodeLanguage(null);
        Vps_Registry::get('trl')->setModel(null, Vps_Trl::SOURCE_WEB);
        parent::tearDown();
    }

    public function testPlaceholding()
    {
        // web code language is 'de', tested language is 'en'

        $c = $this->_root->getPageByUrl('http://'.Vps_Registry::get('config')->server->domain.'/en/testtrl', 'en');

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
    }
}
