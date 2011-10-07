<?php
/**
 * @group Vpc_Trl
 * @group Vpc_Trl_StaticTexts
 * @group Vpc_Trl_StaticTexts_Form
 */
class Vpc_Trl_StaticTextsForm_Test extends Vpc_TestAbstract
{
    public function setUp()
    {
        Vps_Registry::get('config')->languages = array('de', 'en');
        Vps_Trl::getInstance()->setWebCodeLanguage('de');
        Vps_Trl::getInstance()->setModel(new Vpc_Trl_StaticTextsForm_TrlModelWeb(), Vps_Trl::SOURCE_WEB);
        parent::setUp('Vpc_Trl_StaticTextsForm_Root');
    }

    public function tearDown()
    {
        Vps_Trl::getInstance()->setWebCodeLanguage(null);
        Vps_Trl::getInstance()->setModel(null, Vps_Trl::SOURCE_WEB);
        parent::tearDown();
    }

    public function testFormTranslate()
    {
        // web code language is 'de', tested language is 'en'

        $c = $this->_root->getPageByUrl('http://'.Vps_Registry::get('config')->server->domain.'/en/testtrl', 'en');
        $this->assertEquals('en', $c->getLanguage());
        $c->getChildComponent('-child')->getComponent()->processInput(array());

        $render = $c->render();

        $this->assertContains('<label for="form_firstname">Firstname:</label>', $render);
        $this->assertContains('<label for="form_lastname">Lastname:</label>', $render);
        $this->assertContains('<label for="form_company">Company:</label>', $render);

        $this->assertContains('<label for="form_firstname2">Firstname:</label>', $render);
        $this->assertContains('<label for="form_lastname2">Lastname:</label>', $render);
        $this->assertContains('<label for="form_company2">Company:</label>', $render);

        $this->assertContains('<label for="form_company3">Company-Lastname:</label>', $render);
    }
}
