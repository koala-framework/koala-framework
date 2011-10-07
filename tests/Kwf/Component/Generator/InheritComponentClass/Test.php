<?php
class Kwf_Component_Generator_InheritComponentClass_Test extends Kwc_TestAbstract
{
    public function setUp()
    {
        parent::setUp('Kwf_Component_Generator_InheritComponentClass_Root');
    }

    public function testComponentClasses()
    {
        $this->assertEquals($this->_root->componentClass, 'Kwf_Component_Generator_InheritComponentClass_Root');
        $this->assertEquals($this->_root->inheritClasses, array());

        $this->assertEquals($this->_root->getChildComponent('_page1')->inheritClasses,
            array('Kwf_Component_Generator_InheritComponentClass_Root'));

        $this->assertEquals($this->_root->getChildComponent('_page1')
                    ->getChildComponent('_page11')->inheritClasses,
            array('Kwf_Component_Generator_InheritComponentClass_Root'));

        $this->assertEquals($this->_root->getChildComponent('_page2')->inheritClasses,
            array('Kwf_Component_Generator_InheritComponentClass_Root'));

        $this->assertEquals($this->_root->getChildComponent('_page2')
                    ->getChildComponent('-box21')->inheritClasses,
            array());

        $this->assertEquals($this->_root->getChildComponent('_page2')
                    ->getChildComponent('_page21')->inheritClasses,
            array('Kwf_Component_Generator_InheritComponentClass_Page2', 'Kwf_Component_Generator_InheritComponentClass_Root'));

        $this->assertEquals($this->_root->getChildComponent('_page2')
                    ->getChildComponent('-comp21')->inheritClasses,
            array());

        $this->assertEquals($this->_root->getChildComponent('-box1')->inheritClasses,
            array());
    }
}
