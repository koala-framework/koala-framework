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

    public function testChildContains1()
    {
        $model = Kwf_Model_Abstract::getInstance('Kwf_Model_Union_Dependent_Parent');

        $childSelect = new Kwf_Model_Select();
        $childSelect->whereEquals('foo', 'aa');

        $select = new Kwf_Model_Select();
        $select->where(new Kwf_Model_Select_Expr_Child_Contains(
            'TestModel', $childSelect
        ));

        $this->assertEquals(1, count($model->getRows($select)));
        $this->assertEquals(1, $model->countRows($select));
    }

    public function testChildContains2()
    {
        $model = Kwf_Model_Abstract::getInstance('Kwf_Model_Union_Dependent_Parent');

        $childSelect = new Kwf_Model_Select();
        $childSelect->whereEquals('foo', 'xx');

        $select = new Kwf_Model_Select();
        $select->where(new Kwf_Model_Select_Expr_Child_Contains(
            'TestModel', $childSelect
        ));

        $this->assertEquals(2, count($model->getRows($select)));
        $this->assertEquals(2, $model->countRows($select));
    }
}
