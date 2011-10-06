<?php
/**
 * @group Component_Events
 */
class Vps_Component_Events_Test extends Vpc_TestAbstract
{
    public function testTableEvents()
    {
        $root = $this->_init('Vps_Component_Events_Table_Component');

        $events = Vps_Component_Events_Table_Events::getInstance(
            'Vps_Component_Events_Table_Events',
            array('componentClass' => 'Vps_Component_Events_Table_Component')
        );

        $model = Vps_Model_Abstract::getInstance('Vps_Component_Events_Table_Model');
        $count = 0;

        $row = $model->getRow(3);
        $row->visible = 1;
        $row->save();
        $this->assertEquals(++$count, $events->countCalled);

        $row = $model->getRow(3);
        $row->visible = 0;
        $row->save();
        $this->assertEquals(++$count, $events->countCalled);

        $row = $model->createRow(array('name' => 'F6', 'pos' => 5, 'visible' => 1));
        $row->save();
        $this->assertEquals(++$count, $events->countCalled);

        $row = $model->getRow(2);
        $row->delete();
        $this->assertEquals(++$count, $events->countCalled);

        $row = $model->getRow(4);
        $row->pos = 1;
        $row->save();
        $this->assertEquals(++$count, $events->countCalled);

        $row = $model->createRow(array('name' => 'F5', 'pos' => 5, 'visible' => 0));
        $row->save();
        $this->assertEquals($count, $events->countCalled);

        $row = $model->getRow(3);
        $row->delete();
        $this->assertEquals($count, $events->countCalled);

    }

    public function testPagesEvents()
    {
        $root = $this->_init('Vps_Component_Events_Pages_Component');

        $events = Vps_Component_Events_Pages_Events::getInstance(
            'Vps_Component_Events_Pages_Events',
            array('componentClass' => 'Vps_Component_Events_Pages_Component')
        );

        $model = Vps_Model_Abstract::getInstance('Vps_Component_Events_Pages_Model');
        $count = 0;

        $row = $model->getRow(4);
        $row->visible = 1;
        $row->save();
        $this->assertEquals(++$count, $events->countCalled);

        $row = $model->getRow(3);
        $row->parent_id = 'root';
        $row->save();
        $this->assertEquals(++$count, $events->countCalled);
    }
}