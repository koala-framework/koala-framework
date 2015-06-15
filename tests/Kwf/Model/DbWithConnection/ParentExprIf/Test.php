<?php
class Kwf_Model_DbWithConnection_ParentExprIf_Test extends Kwf_Test_TestCase
{
    private $_modelParent;
    private $_modelChild;
    public function setUp()
    {
        $this->_modelChild = Kwf_Model_Abstract::getInstance('Kwf_Model_DbWithConnection_ParentExprIf_ChildModel');
        $this->_modelChild->setUp();
        $this->_modelParent = Kwf_Model_Abstract::getInstance('Kwf_Model_DbWithConnection_ParentExprIf_ParentModel');
        $this->_modelParent->setUp();
        $this->_modelParentSibling = Kwf_Model_Abstract::getInstance('Kwf_Model_DbWithConnection_ParentExprIf_ParentSiblingModel');
        $this->_modelParentSibling->setUp();
    }

    public function tearDown()
    {
        if ($this->_modelParent) $this->_modelParent->dropTable();
        if ($this->_modelChild) $this->_modelChild->dropTable();
        if ($this->_modelParentSibling) $this->_modelParentSibling->dropTable();
    }

    public function testIfLazy()
    {
        $row = $this->_modelChild->getRow(1);
        $this->assertEquals(205, $row->if_field);
    }

    public function testIfEager()
    {
        $s = new Kwf_Model_Select();
        $s->whereId(1);
        $s->expr('if_field');
        $s->expr('if_field_sibling');
        $row = $this->_modelChild->getRow($s);
        $this->assertEquals(205, $row->if_field);
    }

    public function testIfSiblingLazy()
    {
        $row = $this->_modelChild->getRow(1);
        $this->assertEquals(105, $row->if_field_sibling);
    }

    public function testIfSiblingEager()
    {
        $s = new Kwf_Model_Select();
        $s->whereId(1);
        $s->expr('if_field_sibling');
        $row = $this->_modelChild->getRow($s);
        $this->assertEquals(105, $row->if_field_sibling);
    }

    public function testParentLazy()
    {
        $row = $this->_modelChild->getRow(1);
        $this->assertEquals(205, $row->parent_value);
    }

    public function testParentEager()
    {
        $s = new Kwf_Model_Select();
        $s->whereId(1);
        $s->expr('parent_value');
        $row = $this->_modelChild->getRow($s);
        $this->assertEquals(205, $row->parent_value);
    }

    public function testSiblingLazy()
    {
        $row = $this->_modelChild->getRow(1);
        $this->assertEquals(105, $row->sibling_value);
    }

    public function testSiblingEager()
    {
        $s = new Kwf_Model_Select();
        $s->whereId(1);
        $s->expr('sibling_value');
        $row = $this->_modelChild->getRow($s);
        $this->assertEquals(105, $row->sibling_value);
    }

    public function testParentOrder()
    {
        $s = new Kwf_Model_Select();
        $s->order('if_field', 'DESC');
        $row = $this->_modelChild->getRow($s);
        $this->assertEquals(207, $row->if_field);
    }
}
