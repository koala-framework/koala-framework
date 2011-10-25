<?php
/**
 * @group Kwc_Trl
 *
ansicht frontend:
http://doleschal.kwf.niko.vivid/kwf/kwctest/Kwc_Trl_Paragraphs_Root/de/test
http://doleschal.kwf.niko.vivid/kwf/kwctest/Kwc_Trl_Paragraphs_Root/en/test

DE bearbeiten:
http://doleschal.kwf.niko.vivid/kwf/componentedittest/Kwc_Trl_Paragraphs_Root/Kwc_Trl_Paragraphs_Paragraphs_Component?componentId=root-de_test
EN bearbeiten
http://doleschal.kwf.niko.vivid/kwf/componentedittest/Kwc_Trl_Paragraphs_Root/Kwc_Trl_Paragraphs_Paragraphs_Trl_Component.Kwc_Trl_Paragraphs_Paragraphs_Component/?componentId=root-en_test
 */
class Kwc_Trl_Paragraphs_Test extends Kwc_TestAbstract
{
    public function setUp()
    {
        parent::setUp('Kwc_Trl_Paragraphs_Root');
    }
    public function testIt()
    {
        $domain = Zend_Registry::get('config')->server->domain;

        $c = $this->_root->getPageByUrl('http://'.$domain.'/de/test', 'en');
        $this->assertEquals($c->componentId, 'root-master_test');
        $this->assertTrue(substr_count($c->render(), 'child')==3);

        $c = $this->_root->getPageByUrl('http://'.$domain.'/en/test', 'en');
        $this->assertEquals($c->componentId, 'root-en_test');
        $this->assertTrue(substr_count($c->render(), 'child (en)')==1);
    }
}
