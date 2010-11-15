<?php
/**
 * @group Model_MirrorCache
 * @group Model_MirrorCache_Sibling
 */
class Vps_Model_MirrorCache_ModelSiblingTest extends PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->_proxyModel = Vps_Model_Abstract::getInstance('Vps_Model_MirrorCache_MirrorCacheModel');
    }

    public function testNoSyncWhenOnlySavedToSibling()
    {
        sleep(2);
        Vps_Benchmark::enable();
        Vps_Benchmark::reset();

        $newModel = new Vps_Model_MirrorCache_MirrorCacheModel();
        $newModel->synchronize();

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

        $this->assertEquals('sib val 1', $this->_proxyModel->siblingModel->getRow(1)->siblingcol);
    }
}

