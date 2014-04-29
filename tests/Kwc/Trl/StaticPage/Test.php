<?php
class Kwc_Trl_StaticPage_Test extends Kwc_TestAbstract
{
    public function setUp()
    {
        parent::setUp('Kwc_Trl_StaticPage_Root_Component');
    }

    public function testLanguage()
    {
        $this->assertEquals('de', $this->_root->getComponentById('root-master_foo')->getLanguage());
        $this->assertEquals('de', $this->_root->getComponentById('root-master')->getLanguage());
        $this->assertEquals('en', $this->_root->getComponentById('root-en_foo')->getLanguage());
        $this->assertEquals('en', $this->_root->getComponentById('root-en')->getLanguage());
    }

    public function testMaster()
    {
        $this->assertEquals('Sichtbar', $this->_root->getComponentById('root-master_foo')->name);
        $this->assertEquals('sichtbar', $this->_root->getComponentById('root-master_foo')->filename);
    }

    public function testTrl()
    {
        $this->assertEquals('Visible', $this->_root->getComponentById('root-en_foo')->name);
        $this->assertEquals('visible', $this->_root->getComponentById('root-en_foo')->filename);
    }
}
