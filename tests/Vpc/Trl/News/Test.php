<?php
/**
 * @group Vpc_Trl
 *
ansicht frontend:
http://doleschal.vps.niko.vivid/vps/vpctest/Vpc_Trl_News_Root/de/test
http://doleschal.vps.niko.vivid/vps/vpctest/Vpc_Trl_News_Root/en/test

http://doleschal.vps.niko.vivid/vps/componentedittest/Vpc_Trl_News_Root/Vpc_Trl_News_News_Component?componentId=root-master_test
http://doleschal.vps.niko.vivid/vps/componentedittest/Vpc_Trl_News_Root/Vpc_News_Trl_Component.Vpc_Trl_News_News_Component?componentId=root-en_test
 */
class Vpc_Trl_News_Test extends Vpc_TestAbstract
{
    public function setUp()
    {
        parent::setUp('Vpc_Trl_News_Root');
    }

    public function testDe()
    {
        $c = $this->_root->getComponentById('root-master_test');
        $this->assertContains('/de/test/2_lipsum2', $c->render());
        $this->assertContains('/de/test/1_lipsum', $c->render());

        $c = $this->_root->getComponentById('root-en_test');
        $this->assertContains('/en/test/1_loremen', $c->render());
    }
}
