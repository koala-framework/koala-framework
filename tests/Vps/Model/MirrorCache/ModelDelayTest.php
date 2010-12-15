<?php
/**
 * @group Model_MirrorCache
 * @group Model_MirrorCache_Delay
 * @group slow
 */
class Vps_Model_MirrorCache_ModelDelayTest extends Vps_Test_TestCase
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
        $this->_proxyModel = $this->_getNewModel();
    }

    private function _getNewModel()
    {
        return new Vps_Model_MirrorCache(array(
            'proxyModel' => $this->_mirrorModel,
            'sourceModel' => $this->_sourceModel,
            'syncTimeField' => 'timefield',
            'maxSyncDelay' => 2
        ));
    }

    public function testRequests()
    {
        sleep(2);
        Vps_Benchmark::enable();
        Vps_Benchmark::reset();

        $this->_getNewModel()->synchronize();
        $this->assertEquals(1, Vps_Benchmark::getCounterValue('mirror sync'));

        Vps_Benchmark::reset();

        $this->_proxyModel->getRows();
        $this->assertEquals(null, Vps_Benchmark::getCounterValue('mirror sync'));
        sleep(2);
        $this->_proxyModel->getRows();
        $this->assertEquals(1, Vps_Benchmark::getCounterValue('mirror sync'));
        $this->_proxyModel->getIds();
        $this->assertEquals(1, Vps_Benchmark::getCounterValue('mirror sync'));

        Vps_Benchmark::disable();
    }

    public function testUpdate()
    {
        sleep(2);
        Vps_Benchmark::enable();
        Vps_Benchmark::reset();

        // sync with another instance
        $this->_getNewModel()->synchronize();
        $this->assertEquals(1, Vps_Benchmark::getCounterValue('mirror sync'));
        Vps_Benchmark::reset();

        // add a row to source model
        $source = $this->_sourceModel->createRow(array(
            'id' => 4, 'firstname' => 'Vier', 'timefield' => date('Y-m-d H:i:s', time() - 5)
        ));
        $source->save();
        $this->assertEquals(null, Vps_Benchmark::getCounterValue('mirror sync'));
        $this->assertEquals(null, $this->_proxyModel->getRow(4));

        // update a row - at the end the added source row (id=4) must be in proxymodel
        $r = $this->_proxyModel->getRow(2);
        $r->firstname = 'Herbert';
        $this->assertEquals(null, Vps_Benchmark::getCounterValue('mirror sync'));
        $r->save();
        $this->assertEquals(1, Vps_Benchmark::getCounterValue('mirror sync'));
        $this->assertEquals('Vier', $this->_proxyModel->getRow(4)->firstname);
        $this->assertEquals('Vier', $this->_mirrorModel->getRow(4)->firstname);

        Vps_Benchmark::disable();
    }

    public function testCreate()
    {
        sleep(2);
        Vps_Benchmark::enable();
        Vps_Benchmark::reset();

        // sync with another instance
        $this->_getNewModel()->synchronize();
        $this->assertEquals(1, Vps_Benchmark::getCounterValue('mirror sync'));
        Vps_Benchmark::reset();

        // add a row to source model
        $source = $this->_sourceModel->createRow(array(
            'id' => 4, 'firstname' => 'Vier', 'timefield' => date('Y-m-d H:i:s', time() - 5)
        ));
        $source->save();
        $this->assertEquals(null, Vps_Benchmark::getCounterValue('mirror sync'));
        $this->assertEquals(null, $this->_proxyModel->getRow(4));

        // update a row - at the end the added source row (id=4) must be in proxymodel
        $r = $this->_proxyModel->createRow();
        $r->firstname = 'Nadine';
        $r->timefield = date("Y-m-d H:i:s");
        $this->assertEquals(null, Vps_Benchmark::getCounterValue('mirror sync'));
        $r->save();
        $this->assertEquals(1, Vps_Benchmark::getCounterValue('mirror sync'));

        $this->assertEquals('Vier', $this->_proxyModel->getRow(4)->firstname);
        $this->assertEquals('Vier', $this->_mirrorModel->getRow(4)->firstname);
        $this->assertEquals(5, $r->id);
        $this->assertEquals(5, $this->_mirrorModel->getRow(5)->id);
        $this->assertEquals(5, $this->_sourceModel->getRow(5)->id);
        $this->assertEquals('Nadine', $this->_sourceModel->getRow(5)->firstname);

        Vps_Benchmark::disable();
    }
}
