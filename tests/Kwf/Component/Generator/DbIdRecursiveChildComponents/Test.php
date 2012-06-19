<?php
class Kwf_Component_Generator_DbIdRecursiveChildComponents_Test extends Kwc_TestAbstract
{
    public function setUp()
    {
        parent::setUp('Kwf_Component_Generator_DbIdRecursiveChildComponents_Root');
/*
    - Page
        -* Detail with dbIdShortcut
          - Table
            -* Item
                - Page
          - Cards
            - Card1
               - Page
*/
    }

    public function testGenerators()
    {
        $this->assertNotNull($this->_root->getComponentById('root_page-1-table-3_page'));
        $this->assertNotNull($this->_root->getComponentById('root_page-2-cards-child_page'));
    }

    public function testChildPagesFromDetail()
    {
        $p = $this->_root->getComponentById('root_page-1');
                          //table + cards
        $this->assertEquals(2+0, count($p->getChildPages()));

        $p = $this->_root->getComponentById('root_page-2');
        $this->assertEquals(1+1, count($p->getChildPages()));

        $p = $this->_root->getComponentById('root_page-3');
        $this->assertEquals(0+0, count($p->getChildPages()));
    }

    public function testChildPagesFromPage()
    {
        $p = $this->_root->getComponentById('root_page');
        $this->assertEquals(3+1, count($p->getChildPages()));
    }
}
