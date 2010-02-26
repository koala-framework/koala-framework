<?
/**
 * @group Vpc_Trl
 *
http://doleschal.vps.niko.vivid/vps/vpctest/Vpc_Trl_Text_Root/de/text
http://doleschal.vps.niko.vivid/vps/vpctest/Vpc_Trl_Text_Root/en/text

http://doleschal.vps.niko.vivid/vps/componentedittest/Vpc_Trl_Text_Root/Vpc_Trl_Text_Text_Component?componentId=root-de_text
http://doleschal.vps.niko.vivid/vps/componentedittest/Vpc_Trl_Text_Root/Vpc_Basic_Text_Trl_Component.Vpc_Trl_Text_Text_Component?componentId=root-en_text
*/
class Vpc_Trl_Text_Test extends Vpc_TestAbstract
{
    public function setUp()
    {
        parent::setUp('Vpc_Trl_Text_Root');
    }

    public function testIt()
    {
        $c = $this->_root->getPageByUrl('http://'.Vps_Registry::get('testDomain').'/de/text', 'en');
        $this->assertEquals($c->componentId, 'root-de_text');
        $this->assertContains('<p>foo</p>', $c->render());
        $c = $this->_root->getPageByUrl('http://'.Vps_Registry::get('testDomain').'/en/text', 'en');
        $this->assertEquals($c->componentId, 'root-en_text');
        $this->assertContains('<p>fooen</p>', $c->render());
    }
}
