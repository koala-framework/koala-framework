<?php
/**
 * @group Vpc_Basic_LinkTagNews
 **/
class Vpc_Basic_LinkTagNews_Test extends Vpc_TestAbstract
{
    public function setUp()
    {
        parent::setUp('Vpc_Basic_LinkTagNews_Root');
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
