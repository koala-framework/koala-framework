<?php
/**
 * @group Model_MirrorCache
 * @group Model_MirrorCache_Sibling
 */
class Vps_Model_MirrorCache_ModelSiblingTest extends Vps_Test_TestCase
{
    public function testNoSyncWhenOnlySavedToSibling()
    {
        sleep(2);
        Vps_Benchmark::enable();
        Vps_Benchmark::reset();

        $mirror = new Vps_Model_MirrorCache_MirrorCacheModel();

        $r = $mirror->getRow(1);
        $r->siblingcol = 'sib val 1';
        $r->save();


        $r = $mirror->getRow(1);
        $r->firstname = 'Herbert';
        $r->save();
        $this->assertEquals(1, Vps_Benchmark::getCounterValue('mirror sync'));

        Vps_Benchmark::reset();
        Vps_Benchmark::disable();

        $this->assertEquals('sib val 1', $mirror->getRow(1)->siblingcol);
    }
}

