<?php
/**
 * @group Kwc_Trl
 * @group Kwc_Trl_StaticTexts
 * @group Kwc_Trl_StaticTexts_OneLang
 */
class Kwc_Trl_StaticTextsOneLang_Test extends Kwc_TestAbstract
{
    public function setUp()
    {
        Kwf_Registry::get('config')->languages = array('de', 'en');
        Kwf_Trl::getInstance()->setWebCodeLanguage('de');
        parent::setUp('Kwc_Trl_StaticTextsOneLang_Root');
        $trlElements = array();
        $trlElements['web']['de']['Sichtbar-'] = 'Sichtbar';
        $trlElements['web']['de']['Am-time'] = 'Am';
        $trlElements['web']['de']['Antwort-'] = 'Antwort';
        $trlElements['web']['de_plural']['Antworten-'] = 'Antworten';
        $trlElements['web']['de']['Antwort-test'] = 'Antwort';
        $trlElements['web']['de_plural']['Antworten-test'] = 'Antworten';

        $trlElements['kwf']['de']['Visible-'] = 'Sichtbar';
        $trlElements['kwf']['de']['On-time'] = 'Am';
        $trlElements['kwf']['de']['reply-'] = 'Antwort';
        $trlElements['kwf']['de_plural']['replies-'] = 'Antworten';
        $trlElements['kwf']['de']['reply-test'] = 'Antwort';
        $trlElements['kwf']['de_plural']['replies-test'] = 'Antworten';

        Kwf_Trl::getInstance()->setTrlElements($trlElements);
    }

    public function tearDown()
    {
        Kwf_Trl::getInstance()->setWebCodeLanguage(null);
        Kwf_Trl::getInstance()->unsetTrlElements();
        Kwf_Cache_SimpleStatic::clear('trl-');
        Kwf_Cache_SimpleStatic::clear('trlp-');
        parent::tearDown();
    }

    public function testOneLang()
    {
        $c = $this->_root->getPageByUrl('http://'.Kwf_Registry::get('config')->server->domain.'/trltest', 'de');

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
}
