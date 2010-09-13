<?php
/**
 * @group Model_Relations_DependentModelWithArrow
 */
class Vps_Model_Relations_DependentModelWithArrow_Test extends PHPUnit_Framework_TestCase
{
    public function testIt()
    {
        $m = Vps_Model_Abstract::getInstance('Vps_Model_Relations_DependentModelWithArrow_ChildParentModel');
        $row = $m->getRow(200);
        $crow = $row->getChildRows('Child')->current();
        $this->assertEquals($crow->id, 100);
    }
}