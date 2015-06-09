<?php
class Kwf_Component_Plugin_Inherit_Test extends Kwc_TestAbstract
{
    public function setUp()
    {
        parent::setUp('Kwf_Component_Plugin_Inherit_Root_Component');
    }

    /*
    root
     - 1 (test1)
      - 2 (test2, with inherit plugin)
        - 3 (test1)
          - 4 (test1)
     - 5 (test1)
       - 6 (test1)
    */
    public function testInheritPlugin()
    {
        $this->assertEquals('test1',   trim($this->_root->getComponentById(1)->render()));
        $this->assertEquals('replace', trim($this->_root->getComponentById(2)->render()));
        $this->assertEquals('replace', trim($this->_root->getComponentById(3)->render()));
        $this->assertEquals('replace', trim($this->_root->getComponentById(4)->render()));
        $this->assertEquals('test1',   trim($this->_root->getComponentById(5)->render()));
        $this->assertEquals('test1',   trim($this->_root->getComponentById(6)->render()));
    }

    public function testMoveBelowPageWithInheritPlugin()
    {
        $this->_root->getComponentById(5)->render();

        $r = Kwf_Model_Abstract::getInstance('Kwf_Component_Plugin_Inherit_Root_PagesModel')->getRow(5);
        $r->parent_id = 3;
        $r->save();

        $this->_process();

        $this->assertEquals('replace',   trim($this->_root->getComponentById(5)->render()));
    }

    //works anyway as we fire Component_RecursiveRemoved & Component_RecursiveAdded
    public function testChangePageTypeToPageWithInheritPlugin()
    {
        $this->_root->getComponentById(6)->render();

        $r = Kwf_Model_Abstract::getInstance('Kwf_Component_Plugin_Inherit_Root_PagesModel')->getRow(5);
        $r->component = 'test2';
        $r->save();

        $this->_process();

        $this->assertEquals('replace',   trim($this->_root->getComponentById(6)->render()));
    }

    public function testLoginForDownloadBelowInheritPlugin()
    {
        $this->assertEquals(Kwf_Media_Output_IsValidInterface::VALID, Kwf_Component_Plugin_Inherit_Test1_DownloadTag_Component::isValidMediaOutput('1-downloadTag', 'default', 'Kwf_Component_Plugin_Inherit_Test1_DownloadTag_Component'));
        $this->assertEquals(Kwf_Media_Output_IsValidInterface::ACCESS_DENIED, Kwf_Component_Plugin_Inherit_Test1_DownloadTag_Component::isValidMediaOutput('3-downloadTag', 'default', 'Kwf_Component_Plugin_Inherit_Test1_DownloadTag_Component'));
        $this->assertEquals(Kwf_Media_Output_IsValidInterface::ACCESS_DENIED, Kwf_Component_Plugin_Inherit_Test1_DownloadTag_Component::isValidMediaOutput('4-downloadTag', 'default', 'Kwf_Component_Plugin_Inherit_Test1_DownloadTag_Component'));
        $this->assertEquals(Kwf_Media_Output_IsValidInterface::VALID, Kwf_Component_Plugin_Inherit_Test1_DownloadTag_Component::isValidMediaOutput('5-downloadTag', 'default', 'Kwf_Component_Plugin_Inherit_Test1_DownloadTag_Component'));
        $this->assertEquals(Kwf_Media_Output_IsValidInterface::VALID, Kwf_Component_Plugin_Inherit_Test1_DownloadTag_Component::isValidMediaOutput('6-downloadTag', 'default', 'Kwf_Component_Plugin_Inherit_Test1_DownloadTag_Component'));
    }
}
