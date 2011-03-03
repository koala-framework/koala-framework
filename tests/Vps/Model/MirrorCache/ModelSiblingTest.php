<?php
/**
 * @group Model_MirrorCache
 * @group Model_MirrorCache_Sibling
 */
class Vps_Model_MirrorCache_ModelSiblingTest extends Vps_Test_TestCase
{
    private $_sourceModel;
    private $_mirrorModel;
    private $_proxyModel;

    public function setUp()
    {
        parent::setUp();
        $this->_sourceModel = new Vps_Model_FnF(array(
            'uniqueIdentifier' => 'unique',
            'columns' => array('id', 'firstname', 'timefield'),
            'uniqueColumns' => array('id'),
            'data' => array(
                array('id' => 1, 'firstname' => 'Max', 'timefield' => '2008-06-09 00:00:00'),
                array('id' => 2, 'firstname' => 'Susi', 'timefield' => '2008-07-09 10:00:00'),
                array('id' => 3, 'firstname' => 'Kurt', 'timefield' => '2008-07-15 20:00:00')
            )
        ));
        $this->_mirrorModel = new Vps_Model_FnF(array(
            'uniqueIdentifier' => 'unique',
            'columns' => array('id', 'firstname', 'timefield'),
            'uniqueColumns' => array('id'),
            'data' => array(
                array('id' => 1, 'firstname' => 'Max', 'timefield' => '2008-06-09 00:00:00'),
                array('id' => 2, 'firstname' => 'Susi', 'timefield' => '2008-07-09 10:00:00')
            )
        ));
        $this->_siblingModel = new Vps_Model_MirrorCache_SiblingModel(array(
            'uniqueIdentifier' => 'unique',
            'columns' => array('id', 'siblingcol'),
            'uniqueColumns' => array('id'),
            'data' => array(
                array('id' => 1, 'siblingcol' => 'sib1'),
                array('id' => 2, 'siblingcol' => 'sib2')
            )
        ));
        $this->_proxyModel = $this->_getNewModel();
    }

    private function _getNewModel()
    {
        return new Vps_Model_MirrorCache_MirrorCacheModel(array(
            'proxyModel' => $this->_mirrorModel,
            'sourceModel' => $this->_sourceModel,
            'siblingModels' => array($this->_siblingModel),
            'syncTimeField' => 'timefield',
            'maxSyncDelay' => 2
        ));
    }

    public function testNoSyncWhenOnlySavedToSibling()
    {
        sleep(2);
        Vps_Benchmark::enable();
        Vps_Benchmark::reset();

        $this->_getNewModel()->synchronize();
        $r = $this->_proxyModel->getRow(1);
        $r->siblingcol = 'sib val 1';
        $r->save();
        $this->assertEquals(1, Vps_Benchmark::getCounterValue('mirror sync'));
        $r = $this->_proxyModel->getRow(1);
        $r->firstname = 'Herbert';
        $r->save();
        $this->assertEquals(2, Vps_Benchmark::getCounterValue('mirror sync'));

        Vps_Benchmark::reset();
        Vps_Benchmark::disable();

        $this->assertEquals('sib val 1', $this->_siblingModel->getRow(1)->siblingcol);
    }
}

