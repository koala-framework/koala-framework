<?php
/**
 * @group Vpc_Trl
 *
ansicht frontend:
http://doleschal.vps.niko.vivid/vps/vpctest/Vpc_Trl_DownloadTag_Root/de/test1
http://doleschal.vps.niko.vivid/vps/vpctest/Vpc_Trl_DownloadTag_Root/de/test2
http://doleschal.vps.niko.vivid/vps/vpctest/Vpc_Trl_DownloadTag_Root/en/test1
http://doleschal.vps.niko.vivid/vps/vpctest/Vpc_Trl_DownloadTag_Root/en/test2

DE bearbeiten:
http://doleschal.vps.niko.vivid/vps/componentedittest/Vpc_Trl_DownloadTag_Root/Vpc_Trl_DownloadTag_DownloadTag_Component?componentId=root-master_test1
http://doleschal.vps.niko.vivid/vps/componentedittest/Vpc_Trl_DownloadTag_Root/Vpc_Trl_DownloadTag_DownloadTag_Component?componentId=root-master_test2
EN bearbeiten
http://doleschal.vps.niko.vivid/vps/componentedittest/Vpc_Trl_DownloadTag_Root/Vpc_Trl_DownloadTag_DownloadTag_Trl_Component.Vpc_Trl_DownloadTag_DownloadTag_Component?componentId=root-en_test1
http://doleschal.vps.niko.vivid/vps/componentedittest/Vpc_Trl_DownloadTag_Root/Vpc_Trl_DownloadTag_DownloadTag_Trl_Component.Vpc_Trl_DownloadTag_DownloadTag_Component?componentId=root-en_test2
 */
class Vpc_Trl_DownloadTag_Test extends Vpc_TestAbstract
{
    public function setUp()
    {
        parent::setUp('Vpc_Trl_DownloadTag_Root');
    }

    public function testDe()
    {
        $c = $this->_root->getComponentById('root-master_test1');
        $this->assertContains('foo.png', $c->render());

        $c = $this->_root->getComponentById('root-master_test2');
        $this->assertContains('bar.png', $c->render());
    }

    public function testEn()
    {
        $c = $this->_root->getComponentById('root-en_test1');
        $this->assertContains('blub.gif', $c->render());

        $c = $this->_root->getComponentById('root-en_test2');
        $this->assertContains('bar.png', $c->render());
    }
}
