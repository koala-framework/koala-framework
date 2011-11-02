<?php
/**
 * @group Component_Events
 * @group Component_Events_Table
 */
class Kwf_Component_Events_Table_Test extends Kwc_TestAbstract
{
    public function setUp()
    {
        parent::setUp('Kwf_Component_Events_Table_Component');
    }

    public function testEvents()
    {
        $events = Kwf_Component_Events_Table_Events::getInstance(
            'Kwf_Component_Events_Table_Events',
            array('componentClass' => 'Kwf_Component_Events_Table_Component')
        );

        $model = Kwf_Model_Abstract::getInstance('Kwf_Component_Events_Table_Model');
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
}