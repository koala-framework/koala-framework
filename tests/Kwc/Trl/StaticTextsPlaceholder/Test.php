<?php
/**
 * @group Kwc_Trl
 * @group Kwc_Trl_StaticTexts
 * @group Kwc_Trl_StaticTexts_Placeholder
 */
class Kwc_Trl_StaticTextsPlaceholder_Test extends Kwc_TestAbstract
{
    public function setUp()
    {
        Kwf_Registry::get('config')->languages = array('de', 'en');
        Kwf_Trl::getInstance()->setWebCodeLanguage('de');
        parent::setUp('Kwc_Trl_StaticTextsPlaceholder_Root');

        $trlElements = array();
        $trlElements['web']['en'] = array();
        $trlElements['web']['en_plural'] = array();
        $trlElements['web']['en']['Sichtbar-'] = 'Visible';
        $trlElements['web']['en']['Am-time'] = 'On';
        $trlElements['web']['en']['Antwort-'] = 'reply';
        $trlElements['web']['en_plural']['Antworten-'] = 'replies';
        $trlElements['web']['en']['Antwort-test'] = 'reply';
        $trlElements['web']['en_plural']['Antworten-test'] = 'replies';
        $trlElements['kwf']['de'] = array();
        $trlElements['kwf']['de_plural'] = array();

        Kwf_Trl::getInstance()->setTrlElements($trlElements);
    }

    public function tearDown()
    {
        Kwf_Trl::getInstance()->setWebCodeLanguage(null);
        parent::tearDown();
    }

    public function testPlaceholdingWeb()
    {
        // web code language is 'de', tested language is 'en'

        $c = $this->_root->getPageByUrl('http://'.Kwf_Registry::get('config')->server->domain.'/en/testtrl', 'en');

        $this->assertEquals('en', $c->getLanguage());

        $render = $c->render();

        $this->assertContains('trlTest: Visible', $render);
        $this->assertContains('trlcTest: On', $render);
        $this->assertContains('trlpTest1: reply', $render);
        $this->assertContains('trlpTest2: replies', $render);
        $this->assertContains('trlcpTest1: reply', $render);
        $this->assertContains('trlcpTest2: replies', $render);
    }

    public function testPlaceholdingKwf()
    {
        // web code language is 'de', tested language is 'en'

        $c = $this->_root->getPageByUrl('http://'.Kwf_Registry::get('config')->server->domain.'/en/testtrl', 'en');

        $this->assertEquals('en', $c->getLanguage());

        $render = $c->render();

        $this->assertContains('trlKwfTest: Visible', $render);
        $this->assertContains('trlcKwfTest: On', $render);
        $this->assertContains('trlpKwfTest1: reply', $render);
        $this->assertContains('trlpKwfTest2: replies', $render);
        $this->assertContains('trlcpKwfTest1: reply', $render);
        $this->assertContains('trlcpKwfTest2: replies', $render);
    }
}
