<?php
/**
 * @group Model_Relations_MultipleReferencesToSameModel
 */
class Kwf_Model_Relations_MultipleReferencesToSameModel_Test extends Kwf_Test_TestCase
{
    public function testGetParentRow()
    {
        $todo = Kwf_Model_Abstract::getInstance('Kwf_Model_Relations_MultipleReferencesToSameModel_Todo')->getRow(1);
        $this->assertEquals($todo->getParentRow('Creator')->id, 100);
        $this->assertEquals($todo->getParentRow('Assignee')->id, 101);
    }

    public function testGetChildRows()
    {
        $user = Kwf_Model_Abstract::getInstance('Kwf_Model_Relations_MultipleReferencesToSameModel_User')->getRow(100);
        $this->assertEquals($user->getChildRows('TodoCreator')->count(), 1);
        $this->assertEquals($user->getChildRows('TodoAssignee')->count(), 0);

        $user = Kwf_Model_Abstract::getInstance('Kwf_Model_Relations_MultipleReferencesToSameModel_User')->getRow(101);
        $this->assertEquals($user->getChildRows('TodoCreator')->count(), 0);
        $this->assertEquals($user->getChildRows('TodoAssignee')->count(), 1);
    }
}
