<?php
/**
 * @group Kwc_Basic_LinkTagEvent
 **/
class Kwc_Basic_LinkTagEvent_Test extends Kwc_TestAbstract
{
    public function setUp()
    {
        parent::setUp('Kwc_Basic_LinkTagEvent_Root');
    }

    public function testDependsOnRow()
    {
        $eventsComponent = $this->_root->getComponentById(3100);
        $eventsModel = $eventsComponent->getGenerator('detail')->getModel();
        $delRow = $eventsModel->getRow(601);

        $a = Kwc_Admin::getInstance('Kwc_Basic_LinkTagEvent_TestComponent');
        $depends = $a->getComponentsDependingOnRow($delRow);

        $this->assertEquals(1, count($depends));

        $depend = current($depends);
        $this->assertEquals($this->_root->getComponentById(6100)->componentId, $depend->componentId);
    }
}
