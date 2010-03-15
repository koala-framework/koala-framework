<?php
/**
 * @group Vpc_Trl
 *
ansicht frontend:
http://doleschal.vps.niko.vivid/vps/vpctest/Vpc_Trl_LinkTag_Root/de/test1
http://doleschal.vps.niko.vivid/vps/vpctest/Vpc_Trl_LinkTag_Root/de/test2
http://doleschal.vps.niko.vivid/vps/vpctest/Vpc_Trl_LinkTag_Root/en/test1
http://doleschal.vps.niko.vivid/vps/vpctest/Vpc_Trl_LinkTag_Root/en/test2

DE bearbeiten:
http://doleschal.vps.niko.vivid/vps/componentedittest/Vpc_Trl_LinkTag_Root/Vpc_Trl_LinkTag_LinkTag_Component?componentId=root-master_test1
http://doleschal.vps.niko.vivid/vps/componentedittest/Vpc_Trl_LinkTag_Root/Vpc_Trl_LinkTag_LinkTag_Component?componentId=root-master_test2
EN bearbeiten
http://doleschal.vps.niko.vivid/vps/componentedittest/Vpc_Trl_LinkTag_Root/Vpc_Basic_LinkTag_Trl_Component.Vpc_Trl_LinkTag_LinkTag_Component?componentId=root-en_test1
http://doleschal.vps.niko.vivid/vps/componentedittest/Vpc_Trl_LinkTag_Root/Vpc_Basic_LinkTag_Trl_Component.Vpc_Trl_LinkTag_LinkTag_Component?componentId=root-en_test2
 */
class Vpc_Trl_LinkTag_Test extends Vpc_TestAbstract
{
    public function setUp()
    {
        parent::setUp('Vpc_Trl_LinkTag_Root');
    }

    public function testDe()
    {
        $c = $this->_root->getComponentById('root-master_test1');
        $this->assertEquals('', $c->render());

        $c = $this->_root->getComponentById('root-master_test2');
        $this->assertEquals('<a href="http://www.vivid-planet.com/">', $c->render());
    }

    public function testEn()
    {
        $c = $this->_root->getComponentById('root-en_test1');
        $this->assertEquals('', $c->render());

        $c = $this->_root->getComponentById('root-en_test2');
        $this->assertEquals('<a href="http://www.vivid-planet.com/en">', $c->render());
    }
}
