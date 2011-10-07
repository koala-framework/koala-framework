<?php
/**
 * @group Generator_StaticSelect
 */
class Vps_Component_Generator_StaticSelect_Test extends Vpc_TestAbstract
{
    public function setUp()
    {
        parent::setUp('Vps_Component_Generator_StaticSelect_Root');
    }

    public function testSetToFirst()
    {
        $page = $this->_root->getComponentById('root_page1');
        $box = $page->getChildComponent('-box');
        $this->assertNotNull($box);
        $this->assertEquals($box->componentId, 'root_page1-box');
        $this->assertEquals($box->componentClass, 'Vpc_Basic_Empty_Component');
    }

    public function testSetToSecond()
    {
        $page = $this->_root->getComponentById('root_page2');
        $box = $page->getChildComponent('-box');
        $this->assertNotNull($box);
        $this->assertEquals($box->componentId, 'root_page2-box');
        $this->assertEquals($box->componentClass, 'Vps_Component_Generator_StaticSelect_Banner_Component');
    }

    public function testNothingSetShouldUseDefault()
    {
        $page = $this->_root->getComponentById('root_page3');
        $box = $page->getChildComponent('-box');
        $this->assertNotNull($box);
        $this->assertEquals($box->componentId, 'root_page3-box');
        $this->assertEquals($box->componentClass, 'Vpc_Basic_Empty_Component');
    }
}
