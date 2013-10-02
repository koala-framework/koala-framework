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

        $this->_events = Kwf_Component_Events_Table_Events::getInstance(
            'Kwf_Component_Events_Table_Events',
            array('componentClass' => 'Kwf_Component_Events_Table_Component')
        );
        $this->_events->countCalled = 0;
    }

    public function testEvents1()
    {
        $model = Kwf_Model_Abstract::getInstance('Kwf_Component_Events_Table_Model');

        $row = $model->getRow(3);
        $row->visible = 1;
        $row->save();
        $this->assertEquals(2, $this->_events->countCalled);
    }

    public function testEvents2()
    {
        $model = Kwf_Model_Abstract::getInstance('Kwf_Component_Events_Table_Model');

        $row = $model->getRow(2);
        $row->visible = 0;
        $row->save();
        $this->assertEquals(1, $this->_events->countCalled);
    }

    public function testEvents3()
    {
        $model = Kwf_Model_Abstract::getInstance('Kwf_Component_Events_Table_Model');

        $row = $model->createRow(array('name' => 'F6', 'pos' => 5, 'visible' => 1));
        $row->save();
        $this->assertEquals(1, $this->_events->countCalled);
    }

    public function testEvents4()
    {
        $model = Kwf_Model_Abstract::getInstance('Kwf_Component_Events_Table_Model');

        $row = $model->getRow(2);
        $row->delete();
        $this->assertEquals(1, $this->_events->countCalled);
    }

    public function testEvents5()
    {
        $model = Kwf_Model_Abstract::getInstance('Kwf_Component_Events_Table_Model');

        $row = $model->getRow(4);
        $row->pos = 1;
        $row->save();
        $this->assertEquals(1, $this->_events->countCalled);
    }

    public function testEvents6()
    {
        $model = Kwf_Model_Abstract::getInstance('Kwf_Component_Events_Table_Model');

        $row = $model->createRow(array('name' => 'F5', 'pos' => 5, 'visible' => 0));
        $row->save();
        $this->assertEquals(0, $this->_events->countCalled);
    }

    public function testEvents7()
    {
        $model = Kwf_Model_Abstract::getInstance('Kwf_Component_Events_Table_Model');

        $row = $model->getRow(3);
        $row->delete();
        $this->assertEquals(0, $this->_events->countCalled);

    }
}