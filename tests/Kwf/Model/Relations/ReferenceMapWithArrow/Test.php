<?php
/**
 * @group Model_Relations_ReferenceMapWithArrow
 */
class Kwf_Model_Relations_ReferenceMapWithArrow_Test extends Kwf_Test_TestCase
{
    public function testIt()
    {
        $row = Kwf_Model_Abstract::getInstance('Kwf_Model_Relations_ReferenceMapWithArrow_Model')->getRow(1);
        $rows = $row->getChildRows('Child');
        $this->assertEquals(1, count($rows));
        $this->assertEquals(100, $rows->current()->id);
    }
}
