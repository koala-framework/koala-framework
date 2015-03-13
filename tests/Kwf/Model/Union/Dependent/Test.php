<?php
/**
 * @group slow
 */
class Kwf_Model_Union_Dependent_Test extends Kwf_Test_TestCase
{
    public function setUp()
    {
        parent::setUp();
        Kwf_Model_Abstract::getInstance('Kwf_Model_Union_Dependent_Parent')->setUp();
        Kwf_Model_Abstract::getInstance('Kwf_Model_Union_Dependent_Model1')->setUp();
        Kwf_Model_Abstract::getInstance('Kwf_Model_Union_Dependent_Model2')->setUp();
        $this->_m = Kwf_Model_Abstract::getInstance('Kwf_Model_Union_Dependent_TestModel');
    }

    public function tearDown()
    {
        parent::tearDown();
        Kwf_Model_Abstract::getInstance('Kwf_Model_Union_Dependent_Parent')->dropTable();
        Kwf_Model_Abstract::getInstance('Kwf_Model_Union_Dependent_Model1')->dropTable();
        Kwf_Model_Abstract::getInstance('Kwf_Model_Union_Dependent_Model2')->dropTable();
    }

    public function testChildContains()
    {
        $this->markTestIncomplete();

        $model = Kwf_Model_Abstract::getInstance('Kwf_Model_Union_Dependent_Parent');

        $childSelect = new Kwf_Model_Select();
        $childSelect->whereEquals('parent_id', 1);

        $select = new Kwf_Model_Select();
        $select->where(new Kwf_Model_Select_Expr_Child_Contains(
            'TestModel', $childSelect
        ));

        $this->assertEquals(4, $model->countRows($select));
    }
}
