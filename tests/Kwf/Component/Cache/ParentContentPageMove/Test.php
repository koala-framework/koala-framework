<?php
/**
 * @group Kwf_Component_Cache_ParentContentPageMove
 */
class Kwf_Component_Cache_ParentContentPageMove_Test extends Kwc_TestAbstract
{
    public function testMove()
    {
        $root = $this->_init('Kwf_Component_Cache_ParentContentPageMove_Root_Component');
        $c2 = $root->getComponentById('2');
        $this->assertEquals('C1', $c2->render());

        $model = Kwf_Model_Abstract::getInstance('Kwf_Component_Cache_ParentContentPageMove_Root_PagesModel');
        $row = $model->getRow(2);
        $row->parent_id = 3;
        $row->save();
        $this->_process();

        $this->assertEquals('C3', $c2->render());

        $row->parent_id = 1;
        $row->save();
        $this->_process();

        $this->assertEquals('C1', $c2->render());
    }
}
