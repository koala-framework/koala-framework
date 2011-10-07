<?php
/**
 * @group Model_Relations_DependentModelWithArrow
 */
class Kwf_Model_Relations_DependentModelWithArrow_Test extends Kwf_Test_TestCase
{
    public function testIt()
    {
        $m = Kwf_Model_Abstract::getInstance('Kwf_Model_Relations_DependentModelWithArrow_ChildParentModel');
        $row = $m->getRow(200);
        $crow = $row->getChildRows('Child')->current();
        $this->assertEquals($crow->id, 100);
    }
}