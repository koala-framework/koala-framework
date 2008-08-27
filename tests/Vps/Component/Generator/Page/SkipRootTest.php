<?php
class Vps_Component_Generator_Page_SkipRootTest extends PHPUnit_Framework_TestCase
{
    private $_root;
    public function setUp()
    {
        Vps_Registry::get('config')->vpc->rootComponent = 'Vps_Component_Generator_Page_Root';
        $this->_root = Vps_Component_Data_Root::getInstance();
    }

    public function testChilds()
    {
        $c = $this->_root->getChildPages();
        $this->assertEquals(count($c), 1);

        $c = current($c)->getChildPages();
        $this->assertEquals(count($c), 1);

        $c = current($c)->getChildPages();
        $this->assertEquals(count($c), 2);
    }

    public function testSkipRoot()
    {
        $this->assertEquals(count($this->_root->getComponentById(2)
                                    ->getChildPages()), 2);

        $select = new Vps_Component_Select();
        $select->skipRoot();
        $this->assertEquals(count($this->_root->getComponentById(1)
                                    ->getChildPages($select)), 0);
    }
}
