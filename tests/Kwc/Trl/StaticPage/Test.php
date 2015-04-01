<?php
class Kwc_Trl_StaticPage_Test extends Kwc_TestAbstract
{
    public function setUp()
    {
        parent::setUp('Kwc_Trl_StaticPage_Root_Component');
        $trlElements = array();
        $trlElements['kwf']['de']['Visible-'] = 'Sichtbar';
        Kwf_Trl::getInstance()->setTrlElements($trlElements);
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

    public function testGetByFilenameEn()
    {
        $c = $this->_root->getComponentById('root-en');

        $i = $c->getChildComponent(array('filename'=>'visiblex'));
        $this->assertNull($i);
        $i = $c->getChildComponent(array('filename'=>'sichtbar'));
        $this->assertNull($i);

        $i = $c->getChildComponent(array('filename'=>'visible'));
        $this->assertNotNull($i);
        $this->assertEquals($i->componentId, 'root-en_foo');
    }
}
