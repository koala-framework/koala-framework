<?php
/**
 * @group Kwc_Trl
 *
ansicht frontend:
http://doleschal.kwf.niko.vivid/kwf/kwctest/Kwc_Trl_DownloadTag_Root/de/test1
http://doleschal.kwf.niko.vivid/kwf/kwctest/Kwc_Trl_DownloadTag_Root/de/test2
http://doleschal.kwf.niko.vivid/kwf/kwctest/Kwc_Trl_DownloadTag_Root/en/test1
http://doleschal.kwf.niko.vivid/kwf/kwctest/Kwc_Trl_DownloadTag_Root/en/test2

DE bearbeiten:
http://doleschal.kwf.niko.vivid/kwf/componentedittest/Kwc_Trl_DownloadTag_Root/Kwc_Trl_DownloadTag_DownloadTag_Component?componentId=root-master_test1
http://doleschal.kwf.niko.vivid/kwf/componentedittest/Kwc_Trl_DownloadTag_Root/Kwc_Trl_DownloadTag_DownloadTag_Component?componentId=root-master_test2
EN bearbeiten
http://doleschal.kwf.niko.vivid/kwf/componentedittest/Kwc_Trl_DownloadTag_Root/Kwc_Trl_DownloadTag_DownloadTag_Trl_Component.Kwc_Trl_DownloadTag_DownloadTag_Component?componentId=root-en_test1
http://doleschal.kwf.niko.vivid/kwf/componentedittest/Kwc_Trl_DownloadTag_Root/Kwc_Trl_DownloadTag_DownloadTag_Trl_Component.Kwc_Trl_DownloadTag_DownloadTag_Component?componentId=root-en_test2
 */
class Kwc_Trl_DownloadTag_Test extends Kwc_TestAbstract
{
    public function setUp()
    {
        parent::setUp('Kwc_Trl_DownloadTag_Root');
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
