<?php
/**
 * @group Component_ModelObserver
 */
class Kwf_Component_ModelObserver_Test extends Kwf_Test_TestCase
{
    private $_observer;
    private $_model;

    public function setUp()
    {
        parent::setUp();
        $this->markTestIncomplete();
        $this->_observer = Kwf_Component_ModelObserver::getInstance();
        $this->_observer->setEnableProcess(false);
        $this->_observer->setSkipFnF(false);
        $this->_model = new Kwf_Model_FnF(array(
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
        Kwf_Component_ModelObserver::getInstance()->clearInstance();
    }

    public function testAddRow()
    {
        $this->assertEquals(array(), $this->_observer->process());
        $row = $this->_model->createRow(array('component_id' => 4));
        $this->assertEquals(array(), $this->_observer->process());
        $row->save();
        $this->assertEquals(array('Kwf_Model_FnF' => array(4)), $this->_observer->process());
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
        $this->assertEquals(array('Kwf_Model_FnF' => array(1)), $this->_observer->process());
    }

    public function testModel()
    {
        $this->assertEquals(array(), $this->_observer->process());
        Kwf_Component_ModelObserver::getInstance()->add('update', $this->_model);
        $this->assertEquals(array('Kwf_Model_FnF' => array(null)), $this->_observer->process());
    }

    public function testDirtyColumns()
    {
        $fnf = new Kwf_Model_FnF(array(
            'data' => array(
                array('id' => 1, 'value' => 'foo')
            ),
            'columns' => array('id', 'value')
        ));
        $model = new Kwf_Model_Proxy(array('proxyModel' => $fnf));

        // Row ohne Proxy
        $row = $fnf->getRow(1);
        $row->value = 'bar';
        $row->save();
        $process = $this->_observer->getProcess();
        $this->assertEquals(array('value'), $process['update'][0]['dirtyColumns']);
        $this->_observer->process(); // damit process leer ist

        // Row mit Proxy
        $row = $model->getRow(1);
        $row->value = 'foo';
        $row->save();
        $process = $this->_observer->getProcess();
        $this->assertEquals(array('value'), $process['update'][0]['dirtyColumns']);
    }
}
