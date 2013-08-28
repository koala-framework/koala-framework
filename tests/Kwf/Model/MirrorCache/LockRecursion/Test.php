<?php
class Kwf_Model_MirrorCache_LockRecursion_Test extends Kwf_Test_TestCase
{
    public function testIt()
    {
        $m = new Kwf_Model_MirrorCache_LockRecursion_Model();
        $this->assertEquals(2, $m->countRows(array()));
    }
}
