<?php
/**
 * @group Model
 * @group Model_MirrorCache
 */
class Vps_Model_MirrorCache_ModelTest extends Vps_Test_TestCase
{
    private $_sourceModel;
    private $_mirrorModel;
    private $_proxyModel;

    public function setUp()
    {
        parent::setUp();
        $this->_sourceModel = new Vps_Model_FnF(array(
            'uniqueIdentifier' => 'unique',
            'columns' => array('id', 'firstname', 'lastname', 'timefield'),
            'uniqueColumns' => array('id'),
            'data' => array(
                array('id' => 1, 'firstname' => 'Max', 'timefield' => '2008-06-09 00:00:00'),
                array('id' => 2, 'firstname' => 'Susi', 'timefield' => '2008-07-09 10:00:00'),
                array('id' => 3, 'firstname' => 'Kurt', 'timefield' => '2008-07-15 20:00:00')
            )
        ));
        $this->_mirrorModel = new Vps_Model_FnF(array(
            'uniqueIdentifier' => 'unique',
            'columns' => array('id', 'firstname', 'lastname', 'timefield'),
            'uniqueColumns' => array('id'),
            'data' => array(
                array('id' => 1, 'firstname' => 'Max', 'timefield' => '2008-06-09 00:00:00'),
                array('id' => 2, 'firstname' => 'Susi', 'timefield' => '2008-07-09 10:00:00')
            )
        ));
        $this->_proxyModel = new Vps_Model_MirrorCache(array(
            'proxyModel' => $this->_mirrorModel,
            'sourceModel' => $this->_sourceModel,
            'syncTimeField' => 'timefield',
            'maxSyncDelay' => 0
        ));
    }

    public function testRequests()
    {
        Vps_Benchmark::enable();
        Vps_Benchmark::reset();

        $this->_proxyModel->getRows();
        $this->assertEquals(1, Vps_Benchmark::getCounterValue('mirror sync'));
        $this->_proxyModel->getRow(3);
        $this->assertEquals(1, Vps_Benchmark::getCounterValue('mirror sync'));
        $this->_proxyModel->getIds();
        $this->assertEquals(1, Vps_Benchmark::getCounterValue('mirror sync'));
        $this->_proxyModel->countRows();
        $this->assertEquals(1, Vps_Benchmark::getCounterValue('mirror sync'));

        Vps_Benchmark::disable();
    }

    public function testInitialSync()
    {
        $m = $this->_getModel();
        $r = $m->getRow(3);
        $this->assertEquals(3, $r->id);
        $this->assertEquals('Kurt', $r->firstname);
    }

    public function testUpdate()
    {
        $r = $this->_getModel()->getRow(2);
        $r->firstname = 'Herbert';
        $this->assertEquals('Herbert', $r->firstname);
        $this->assertEquals('Herbert', $this->_mirrorModel->getRow(2)->firstname);
        $r->save();
        $this->assertEquals('Herbert', $this->_sourceModel->getRow(2)->firstname);
    }

    public function testCreate()
    {
        $r = $this->_getModel()->createRow();
        $r->firstname = 'Nadine';
        $r->timefield = date("Y-m-d H:i:s");
        $r->save();

        $this->assertEquals(4, $r->id);
        $this->assertEquals(4, $this->_mirrorModel->getRow(4)->id);
        $this->assertEquals(4, $this->_sourceModel->getRow(4)->id);
        $this->assertEquals('Nadine', $this->_sourceModel->getRow(4)->firstname);
    }

    public function testCreateWithPrimaryKey()
    {
        $this->setExpectedException("Vps_Exception_NotYetImplemented");

        $r = $this->_getModel()->createRow();
        $r->firstname = 'Nadine';
        $r->timefield = date("Y-m-d H:i:s");
        $r->id = 8; // primary keys können nicht gesetzt werden
    }

    public function testUpdateWithPrimaryKey()
    {
        $this->setExpectedException("Vps_Exception_NotYetImplemented");

        $r = $this->_getModel()->getRow(2);
        $r->firstname = 'Nadine';
        $r->timefield = date("Y-m-d H:i:s");
        $r->id = 8; // primary keys können nicht gesetzt werden
    }

    private function _getModel()
    {
        return $this->_proxyModel;
    }
}