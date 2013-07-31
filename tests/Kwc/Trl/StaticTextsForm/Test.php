<?php
/**
 * @group Kwc_Trl
 * @group Kwc_Trl_StaticTexts
 * @group Kwc_Trl_StaticTexts_Form
 */
class Kwc_Trl_StaticTextsForm_Test extends Kwc_TestAbstract
{
    public function setUp()
    {
        Kwf_Registry::get('config')->languages = array('de', 'en');
        Kwf_Trl::getInstance()->setWebCodeLanguage('de');
        Kwf_Trl::getInstance()->setModel(new Kwc_Trl_StaticTextsForm_TrlModelWeb(), Kwf_Trl::SOURCE_WEB);
        parent::setUp('Kwc_Trl_StaticTextsForm_Root');
    }

    public function tearDown()
    {
        Kwf_Trl::getInstance()->setWebCodeLanguage(null);
        Kwf_Trl::getInstance()->setModel(null, Kwf_Trl::SOURCE_WEB);
        parent::tearDown();
    }

    public function testFormTranslate()
    {
        // web code language is 'de', tested language is 'en'

        $c = $this->_root->getPageByUrl('http://'.Kwf_Registry::get('config')->server->domain.'/en/testtrl', 'en');
        $this->assertEquals('en', $c->getLanguage());
        $c->getChildComponent('-child')->getComponent()->processInput(array());

        $render = $c->render();

        $this->assertContains('<label for="root-en_testtrl-child_form_firstname">Firstname', $render);
        $this->assertContains('<label for="root-en_testtrl-child_form_lastname">Lastname', $render);
        $this->assertContains('<label for="root-en_testtrl-child_form_company">Company', $render);

        $this->assertContains('<label for="root-en_testtrl-child_form_firstname2">Firstname', $render);
        $this->assertContains('<label for="root-en_testtrl-child_form_lastname2">Lastname', $render);
        $this->assertContains('<label for="root-en_testtrl-child_form_company2">Company', $render);

        $this->assertContains('<label for="root-en_testtrl-child_form_company3">Company-Lastname', $render);
    }
}
