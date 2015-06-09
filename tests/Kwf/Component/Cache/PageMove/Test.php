<?php
/**
 * @group Kwf_Component_Cache_PageMove
 */
class Kwf_Component_Cache_PageMove_Test extends Kwc_TestAbstract
{
    public function testMove()
    {
        $root = $this->_init('Kwf_Component_Cache_PageMove_Root_Component');
        $c2 = $root->getComponentById('2');
        $c2->render();

        $model = Kwf_Model_Abstract::getInstance('Kwf_Component_Cache_PageMove_Root_PagesModel');
        $row = $model->getRow(2);
        $row->parent_id = 3;
        $row->save();
        $this->_process();

        Kwf_Events_Dispatcher::fireEvent(new Kwf_Component_Event_Component_RecursiveContentChanged(
            'Kwc_Basic_Empty_Component', Kwf_Component_Data_Root::getInstance()->getComponentById(3)
        ));
        $this->_process();

        $this->assertNull(Kwf_Component_Cache::getInstance()->load('2'));
    }
}
