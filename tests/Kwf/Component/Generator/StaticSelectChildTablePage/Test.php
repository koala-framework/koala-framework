<?php
class Kwf_Component_Generator_StaticSelectChildTablePage_Test extends Kwc_TestAbstract
{
    public function setUp()
    {
        parent::setUp('Kwf_Component_Generator_StaticSelectChildTablePage_Root_Component');
    }

    public function testComponentClassConstraint1()
    {
        $child1 = $this->_root->getComponentByDbId('root_page2-banner-2', array(
            'componentClass' => 'Kwf_Component_Generator_StaticSelectChildTablePage_Banner2_Child_Component'
        ));
        $this->assertTrue(!!$child1);
        $child2 = $this->_root->getComponentByDbId('root_page2-banner-2', array(
            'componentClass' => 'Kwf_Component_Generator_StaticSelectChildTablePage_Banner1_Child_Component'
        ));
        $this->assertTrue(!$child2);
    }

    public function testComponentClassConstraint2()
    {
        $banner1 = Kwf_Component_Data_Root::getInstance()
            ->getComponentsByDbId('root_page1-banner', array(
                'componentClass' => 'Kwf_Component_Generator_StaticSelectChildTablePage_Banner1_Component'
            ));
        $this->assertTrue(!!$banner1);

        $banner2 = Kwf_Component_Data_Root::getInstance()
            ->getComponentsByDbId('root_page1-banner', array(
                'componentClass' => 'Kwf_Component_Generator_StaticSelectChildTablePage_Banner2_Component'
            ));
        $this->assertTrue(!$banner2);
    }

    public function testPage1()
    {

        $p1 = $this->_root->getComponentById('root_page1');
        $banner = $p1->getChildComponent('-banner');
        $this->assertEquals($banner->componentClass, 'Kwf_Component_Generator_StaticSelectChildTablePage_Banner1_Component');
        $this->assertEquals(count($banner->getChildComponents()), 0);
        $this->assertEquals(count($p1->getChildPages()), 0);
    }

    public function testPage2()
    {
        $p1 = $this->_root->getComponentById('root_page2');
        $banner = $p1->getChildComponent('-banner');
        $this->assertEquals($banner->componentClass, 'Kwf_Component_Generator_StaticSelectChildTablePage_Banner2_Component');
        $this->assertEquals(count($banner->getChildComponents()), 1);
        $child = $banner->getChildComponent('-2');
        $this->assertEquals($child->componentClass, 'Kwf_Component_Generator_StaticSelectChildTablePage_Banner2_Child_Component');
        $this->assertEquals(count($child->getChildComponents()), 1);
        $this->assertEquals(count($banner->getChildPages()), 1);
    }
}
