<?php
/**
 * @group Model_Relations_ReferenceMapWithArrow
 */
class Vps_Model_Relations_ReferenceMapWithArrow_Test extends Vps_Test_TestCase
{
    public function testIt()
    {
        $row = Vps_Model_Abstract::getInstance('Vps_Model_Relations_ReferenceMapWithArrow_Model')->getRow(1);
        $rows = $row->getChildRows('Child');
        $this->assertEquals(1, count($rows));
        $this->assertEquals(100, $rows->current()->id);
    }
}
