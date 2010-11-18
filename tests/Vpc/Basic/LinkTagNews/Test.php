<?php
/**
 * @group Vpc_Basic_LinkTagNews
 **/
class Vpc_Basic_LinkTagNews_Test extends PHPUnit_Framework_TestCase
{
    private $_root;

    public function setUp()
    {
        Vps_Component_Data_Root::setComponentClass('Vpc_Basic_LinkTagNews_Root');
        $this->_root = Vps_Component_Data_Root::getInstance();
    }

    public function testDependsOnRow()
    {
        $newsComponent = $this->_root->getComponentById(2100);
        $newsModel = $newsComponent->getGenerator('detail')->getModel();
        $delRow = $newsModel->getRow(501);

        $a = Vpc_Admin::getInstance('Vpc_Basic_LinkTagNews_TestComponent');
        $depends = $a->getComponentsDependingOnRow($delRow);

        $this->assertEquals(1, count($depends));

        $depend = current($depends);
        $this->assertEquals($this->_root->getComponentById(5100)->componentId, $depend->componentId);
    }
}
