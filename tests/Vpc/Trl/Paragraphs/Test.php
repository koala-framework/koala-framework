<?php
/**
 * @group Vpc_Trl
 *
ansicht frontend:
http://doleschal.vps.niko.vivid/vps/vpctest/Vpc_Trl_Paragraphs_Root/de/test
http://doleschal.vps.niko.vivid/vps/vpctest/Vpc_Trl_Paragraphs_Root/en/test

DE bearbeiten:
http://doleschal.vps.niko.vivid/vps/componentedittest/Vpc_Trl_Paragraphs_Root/Vpc_Trl_Paragraphs_Paragraphs_Component?componentId=root-de_test
EN bearbeiten
http://doleschal.vps.niko.vivid/vps/componentedittest/Vpc_Trl_Paragraphs_Root/Vpc_Trl_Paragraphs_Paragraphs_Trl_Component.Vpc_Trl_Paragraphs_Paragraphs_Component/?componentId=root-en_test
 */
class Vpc_Trl_Paragraphs_Test extends Vpc_TestAbstract
{
    public function setUp()
    {
        parent::setUp('Vpc_Trl_Paragraphs_Root');
    }
    public function testIt()
    {
        $c = $this->_root->getPageByUrl('http://'.Vps_Registry::get('testDomain').'/de/test', 'en');
        $this->assertEquals($c->componentId, 'root-de_test');
        $this->assertTrue(substr_count($c->render(), 'child')==3);

        $c = $this->_root->getPageByUrl('http://'.Vps_Registry::get('testDomain').'/en/test', 'en');
        $this->assertEquals($c->componentId, 'root-en_test');
        $this->assertTrue(substr_count($c->render(), 'child (en)')==1);
    }
}
