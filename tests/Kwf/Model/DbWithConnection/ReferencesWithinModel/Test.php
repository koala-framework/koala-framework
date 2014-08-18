<?php
class Kwf_Model_DbWithConnection_ReferencesWithinModel_Test extends Kwf_Test_TestCase
{
    private $_model;
    public function setUp()
    {
        $this->_model = Kwf_Model_Abstract::
            getInstance('Kwf_Model_DbWithConnection_ReferencesWithinModel_Model');
        $this->_model->setUp();
    }
    public function tearDown()
    {
        $this->_model->dropTable();
        Kwf_Model_Abstract::clearAllRows();
    }

    public function testParentExpression()
    {
        $s = new Kwf_Model_Select();
        $s->whereEquals('id', 2);
        $s->expr('parent_bar');
        $row = $this->_model->getRow($s);
        $this->assertEquals(5, $row->parent_bar);
    }

    public function testParentExpressionLazy()
    {
        $row = $this->_model->getRow(2);
        $this->assertEquals(5, $row->parent_bar);
    }
}
