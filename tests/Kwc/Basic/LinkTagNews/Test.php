<?php
/**
 * @group Kwc_Basic_LinkTagNews
 **/
class Kwc_Basic_LinkTagNews_Test extends Kwc_TestAbstract
{
    public function setUp()
    {
        parent::setUp('Kwc_Basic_LinkTagNews_Root');
    }

    public function testDependsOnRow()
    {
        $newsComponent = $this->_root->getComponentById(2100);
        $newsModel = $newsComponent->getGenerator('detail')->getModel();
        $delRow = $newsModel->getRow(501);

        $a = Kwc_Admin::getInstance('Kwc_Basic_LinkTagNews_TestComponent');
        $depends = $a->getComponentsDependingOnRow($delRow);

        $this->assertEquals(1, count($depends));

        $depend = current($depends);
        $this->assertEquals($this->_root->getComponentById(5100)->componentId, $depend->componentId);
    }
}
