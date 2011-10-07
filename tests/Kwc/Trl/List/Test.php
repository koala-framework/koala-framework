<?php
/**
 * @group Vpc_Trl
 *
ansicht frontend:
http://doleschal.vps.niko.vivid/vps/vpctest/Vpc_Trl_List_Root/de/test
http://doleschal.vps.niko.vivid/vps/vpctest/Vpc_Trl_List_Root/en/test

DE bearbeiten:
http://doleschal.vps.niko.vivid/vps/componentedittest/Vpc_Trl_List_Root/Vpc_Trl_List_List_Component?componentId=root-master_test
EN bearbeiten
http://doleschal.vps.niko.vivid/vps/componentedittest/Vpc_Trl_List_Root/Vpc_Trl_List_List_Trl_Component.Vpc_Trl_List_List_Component/?componentId=root-en_test
 */
class Vpc_Trl_List_Test extends Vpc_TestAbstract
{
    public function setUp()
    {
        parent::setUp('Vpc_Trl_List_Root');
    }
    public function testIt()
    {
        $c = $this->_root->getComponentById('root-master_test');
        $this->assertTrue(substr_count($c->render(), 'foo')==3);

        $c = $this->_root->getComponentById('root-en_test');
        $this->assertTrue(substr_count($c->render(), 'foo')==1);
    }
}
