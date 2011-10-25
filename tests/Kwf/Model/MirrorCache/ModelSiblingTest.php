<?php
/**
 * @group Model_MirrorCache
 * @group Model_MirrorCache_Sibling
 */
class Kwf_Model_MirrorCache_ModelSiblingTest extends Kwf_Test_TestCase
{
    public function testNoSyncWhenOnlySavedToSibling()
    {
        sleep(2);
        Kwf_Benchmark::enable();
        Kwf_Benchmark::reset();

        $mirror = new Kwf_Model_MirrorCache_MirrorCacheModel();

        $r = $mirror->getRow(1);
        $r->siblingcol = 'sib val 1';
        $r->save();


        $r = $mirror->getRow(1);
        $r->firstname = 'Herbert';
        $r->save();
        $this->assertEquals(1, Kwf_Benchmark::getCounterValue('mirror sync'));

        Kwf_Benchmark::reset();
        Kwf_Benchmark::disable();

        $this->assertEquals('sib val 1', $mirror->getRow(1)->siblingcol);
    }
}

