<?php
/**
 * @group Component_ModelObserver
 */
class Vps_Component_ModelObserver_Test extends Vps_Test_TestCase
{
    private $_observer;
    private $_model;

    public function setUp()
    {
        parent::setUp();
        Vps_Component_Data_Root::setComponentClass('Vps_Component_Data_Root');
        $this->_observer = Vps_Component_ModelObserver::getInstance();
        $this->_observer->setEnableProcess(false);
        $this->_observer->clear();
        $this->_observer->setSkipFnF(false);
        $this->_model = new Vps_Model_FnF(array(
            'columns' => array('component_id'),
            'primaryKey' => 'component_id',
            'data' => array(
                array('component_id' => '1'),
                array('component_id' => '2')
            )
        ));
    }

    public function tearDown()
    {
        Vps_Component_ModelObserver::getInstance()->clearInstance();
    }

    public function testAddRow()
    {
        $this->assertEquals(array(), $this->_observer->process());
        $row = $this->_model->createRow(array('component_id' => 4));
        $this->assertEquals(array(), $this->_observer->process());
        $row->save();
        $this->assertEquals(array('Vps_Model_FnF' => array(4)), $this->_observer->process());
        $this->assertEquals(array(), $this->_observer->process());
    }

    public function testDeleteRow()
    {
        $this->assertEquals(array(), $this->_observer->process());
        $this->_model->getRow(1)->delete();
        $this->_model->getRow(2)->delete();
        $this->assertEquals(array(), $this->_observer->process());
    }

    public function testSaveRow()
    {
        $this->assertEquals(array(), $this->_observer->process());
        $this->_model->getRow(1)->save();
        $this->assertEquals(array('Vps_Model_FnF' => array(1)), $this->_observer->process());
    }

    public function testModel()
    {
        $this->assertEquals(array(), $this->_observer->process());
        Vps_Component_ModelObserver::getInstance()->add('update', $this->_model);
        $this->assertEquals(array('Vps_Model_FnF' => array(null)), $this->_observer->process());
    }
}
