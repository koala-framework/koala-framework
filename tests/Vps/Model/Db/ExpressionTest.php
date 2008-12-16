<?php
/**
 * @group Model_Db_Expr
 */
class Vps_Model_Db_ExpressionTest extends PHPUnit_Framework_TestCase
{
    private $_table;
    private $_dbSelect;
    private $_model;

    public function setUp()
    {
        $this->_table = $this->getMock('Vps_Model_Db_Table',
            array('_setupMetadata', '_setupPrimaryKey'),
            array('db' => new Vps_Model_Db_TestAdapter()), '', true);

        $this->_model = new Vps_Model_Db(array(
            'table' => $this->_table
        ));
    }

    public function testExprEquals()
    {
        $select = $this->_model->select()->where(new Vps_Model_Select_Expr_Equals('foo', 'aaa'));
        $this->assertEquals("SELECT \"testtable\".* FROM \"testtable\" WHERE (foo LIKE 'aaa')",
            $this->_model->createDbSelect($select)->__toString());
    }

    public function testExprSmaller()
    {
        $select = $this->_model->select()->where(new Vps_Model_Select_Expr_Smaller('foo', 'aaa'));
        $this->assertEquals("SELECT \"testtable\".* FROM \"testtable\" WHERE (foo < 'aaa')",
            $this->_model->createDbSelect($select)->__toString());
    }

    public function testExprHigher()
    {
        $select = $this->_model->select()->where(new Vps_Model_Select_Expr_Higher('foo', 'aaa'));
        $this->assertEquals("SELECT \"testtable\".* FROM \"testtable\" WHERE (foo > 'aaa')",
            $this->_model->createDbSelect($select)->__toString());
    }

    public function testExprContains()
    {
        $select = $this->_model->select()->where(new Vps_Model_Select_Expr_Contains('foo', 'aaa'));
        $this->assertEquals("SELECT \"testtable\".* FROM \"testtable\" WHERE (foo LIKE '%aaa%')",
            $this->_model->createDbSelect($select)->__toString());
    }

    public function testExprSmallerDate()
    {
        $select = $this->_model->select()->where(new Vps_Model_Select_Expr_SmallerDate('foo', '2008-06-06'));
        $this->assertEquals("SELECT \"testtable\".* FROM \"testtable\" WHERE (foo < '2008-06-06')",
            $this->_model->createDbSelect($select)->__toString());
    }

    public function testExprHigherDate()
    {
        $select = $this->_model->select()->where(new Vps_Model_Select_Expr_HigherDate('foo', '2008-06-06'));
        $this->assertEquals("SELECT \"testtable\".* FROM \"testtable\" WHERE (foo > '2008-06-06')",
            $this->_model->createDbSelect($select)->__toString());
    }

    public function testExprOr()
    {
        $expr = new Vps_Model_Select_Expr_Or(array(
            new Vps_Model_Select_Expr_Equals('foo', 'aaa'),
            new Vps_Model_Select_Expr_Equals('foo', 'bbb')

        ));
        $select = $this->_model->select()->where($expr);
        $this->assertEquals("SELECT \"testtable\".* FROM \"testtable\" WHERE ((foo LIKE 'aaa') OR (foo LIKE 'bbb'))",
            $this->_model->createDbSelect($select)->__toString());
    }

    public function testExprAnd()
    {
        $expr = new Vps_Model_Select_Expr_And(array(
            new Vps_Model_Select_Expr_Equals('foo', 'aaa'),
            new Vps_Model_Select_Expr_Equals('foo', 'bbb')

        ));
        $select = $this->_model->select()->where($expr);
        $this->assertEquals("SELECT \"testtable\".* FROM \"testtable\" WHERE ((foo LIKE 'aaa') AND (foo LIKE 'bbb'))",
            $this->_model->createDbSelect($select)->__toString());
    }

    public function testExprNot()
    {
        $expr = new Vps_Model_Select_Expr_Not(new Vps_Model_Select_Expr_Equals('foo', 'aaa'));
        $select = $this->_model->select()->where($expr);
        $this->assertEquals("SELECT \"testtable\".* FROM \"testtable\" WHERE (NOT (foo LIKE 'aaa'))",
            $this->_model->createDbSelect($select)->__toString());
    }

	public function testExprAndException()
    {
		$this->setExpectedException('Vps_Exception');
        $expr = new Vps_Model_Select_Expr_And(array());
        $select = $this->_model->select()->where($expr);
		$this->_model->createDbSelect($select);
    }

    public function testBigExpr()
    {
        $expr = new Vps_Model_Select_Expr_And(array(
            new Vps_Model_Select_Expr_Equals('foo', 'aaa'),
            new Vps_Model_Select_Expr_Equals('foo', 'bbb')

        ));
        $expr2 = new Vps_Model_Select_Expr_Or(array(
            new Vps_Model_Select_Expr_Equals('foo', 'aaa'),
            new Vps_Model_Select_Expr_Equals('foo', 'bbb')

        ));
        $expr3 = new Vps_Model_Select_Expr_Not(new Vps_Model_Select_Expr_Equals('foo', 'aaa'));
        $expr4 = new Vps_Model_Select_Expr_Or(array($expr, $expr2, $expr3));

        $select = $this->_model->select()->where($expr4);
        $this->assertEquals("SELECT \"testtable\".* FROM \"testtable\" WHERE (((foo LIKE 'aaa') AND ".
         "(foo LIKE 'bbb')) OR ((foo LIKE 'aaa') OR (foo LIKE 'bbb')) OR (NOT (foo LIKE 'aaa')))",
            $this->_model->createDbSelect($select)->__toString());

    }
}
