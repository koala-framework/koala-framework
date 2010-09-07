<?php
/**
 * @group Model_Relations_MultipleReferencesToSameModel
 */
class Vps_Model_Relations_MultipleReferencesToSameModel_Test extends PHPUnit_Framework_TestCase
{
    public function testGetParentRow()
    {
        $todo = Vps_Model_Abstract::getInstance('Vps_Model_Relations_MultipleReferencesToSameModel_Todo')->getRow(1);
        $this->assertEquals($todo->getParentRow('Creator')->id, 100);
        $this->assertEquals($todo->getParentRow('Assignee')->id, 101);
    }

    public function testGetChildRows()
    {
        $this->markTestIncomplete();
        $user = Vps_Model_Abstract::getInstance('Vps_Model_Relations_MultipleReferencesToSameModel_User')->getRow(1);
        $user->getChildRows('tja');
    }
}
