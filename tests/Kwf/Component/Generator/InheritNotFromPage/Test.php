<?php
/**
 * @group Generator
 * @group GeneratorInherit
 */
class Kwf_Component_Generator_InheritNotFromPage_Test extends Kwc_TestAbstract
{
    public function setUp()
    {
        parent::setUp('Kwf_Component_Generator_InheritNotFromPage_Root');
    }

    public function testTitleBox()
    {
        $c = $this->_root->getComponentById('root_page')->getChildComponents();
        $ids = array_keys($c);
        sort($ids);
        $this->assertEquals(array('root_page-box', 'root_page-child', 'root_page-title'), $ids);
    }

    public function testInheritedCorrectClass()
    {
        $c = $this->_root->getComponentById('root_page-child_page2')->getChildComponents();
        $ids = array_keys($c);
        sort($ids);
        $this->assertEquals(array('root_page-child_page2-box', 'root_page-child_page2-title'), $ids);
        $this->assertEquals($c['root_page-child_page2-box']->componentClass, 'Kwf_Component_Generator_Inherit_Box');
    }
}
