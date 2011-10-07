<?
/**
 * @group Kwc_Trl
 *
http://doleschal.kwf.niko.vivid/kwf/kwctest/Kwc_Trl_Text_Root/de/text
http://doleschal.kwf.niko.vivid/kwf/kwctest/Kwc_Trl_Text_Root/en/text

http://doleschal.kwf.niko.vivid/kwf/componentedittest/Kwc_Trl_Text_Root/Kwc_Trl_Text_Text_Component?componentId=root-de_text
http://doleschal.kwf.niko.vivid/kwf/componentedittest/Kwc_Trl_Text_Root/Kwc_Basic_Text_Trl_Component.Kwc_Trl_Text_Text_Component?componentId=root-en_text
*/
class Kwc_Trl_Text_Test extends Kwc_TestAbstract
{
    public function setUp()
    {
        parent::setUp('Kwc_Trl_Text_Root');
    }

    public function testDe()
    {
        $domain = Kwf_Registry::get('config')->server->domain;
        $c = $this->_root->getPageByUrl('http://'.$domain.'/de/text', 'en');
        $this->assertEquals($c->componentId, 'root-master_text');
        $this->assertContains('<p>foo</p>', $c->render());
    }

    public function testEn()
    {
        $domain = Kwf_Registry::get('config')->server->domain;
        $c = $this->_root->getPageByUrl('http://'.$domain.'/en/text', 'en');
        $this->assertEquals($c->componentId, 'root-en_text');
        $this->assertContains('<p>fooen</p>', $c->render());
    }
}
