<?php
/**
 * @group Model_Db_Expr
 */
class Kwf_Model_Db_ExpressionTest extends Kwf_Test_TestCase
{
    private $_table;
    private $_dbSelect;
    private $_model;

    public function setUp()
    {
        parent::setUp();
        $this->_table = $this->getMock('Kwf_Model_Db_Table',
            array('_setupMetadata', '_setupPrimaryKey'),
            array('db' => new Kwf_Model_Db_TestAdapter()), '', true);

        $this->_model = new Kwf_Model_Db(array(
            'table' => $this->_table
        ));
    }

    public function testExprNull()
    {
        $select = $this->_model->select()->where(new Kwf_Model_Select_Expr_IsNull('foo'));
        $this->assertEquals("SELECT \"testtable\".* FROM \"testtable\" WHERE (testtable.foo IS NULL)",
            $this->_model->createDbSelect($select)->__toString());
    }

    public function testExprEquals()
    {
        $select = $this->_model->select()->where(new Kwf_Model_Select_Expr_Equal('foo', 'aaa'));
        $this->assertEquals("SELECT \"testtable\".* FROM \"testtable\" WHERE (testtable.foo = 'aaa')",
            $this->_model->createDbSelect($select)->__toString());
    }

    public function testExprEqualsIn()
    {
        $select = $this->_model->select()->where(new Kwf_Model_Select_Expr_Equal('foo', array('aaa', "bb'b")));
        $this->assertEquals("SELECT \"testtable\".* FROM \"testtable\" WHERE (testtable.foo IN ('aaa','bb\'b'))",
            $this->_model->createDbSelect($select)->__toString());
    }

    public function testExprEqualsEscaping()
    {
        $select = $this->_model->select()->where(new Kwf_Model_Select_Expr_Equal('foo', 'a\'aa'));
        $this->assertEquals("SELECT \"testtable\".* FROM \"testtable\" WHERE (testtable.foo = 'a\'aa')",
            $this->_model->createDbSelect($select)->__toString());
    }

    public function testExprSmaller()
    {
        $select = $this->_model->select()->where(new Kwf_Model_Select_Expr_Lower('foo', 'aaa'));
        $this->assertEquals("SELECT \"testtable\".* FROM \"testtable\" WHERE (testtable.foo < 'aaa')",
            $this->_model->createDbSelect($select)->__toString());
    }

    public function testExprHigher()
    {
        $select = $this->_model->select()->where(new Kwf_Model_Select_Expr_Higher('foo', 'aaa'));
        $this->assertEquals("SELECT \"testtable\".* FROM \"testtable\" WHERE (testtable.foo > 'aaa')",
            $this->_model->createDbSelect($select)->__toString());
    }

    public function testExprContains()
    {
        $select = $this->_model->select()->where(new Kwf_Model_Select_Expr_Contains('foo', 'aaa'));
        $this->assertEquals("SELECT \"testtable\".* FROM \"testtable\" WHERE (testtable.foo LIKE '%aaa%')",
            $this->_model->createDbSelect($select)->__toString());
    }

    public function testExprContainsEscaping()
    {
        $select = $this->_model->select()->where(new Kwf_Model_Select_Expr_Contains('foo', 'a%aa'));
        $this->assertEquals("SELECT \"testtable\".* FROM \"testtable\" WHERE (testtable.foo LIKE '%a\%aa%')",
        $this->_model->createDbSelect($select)->__toString());

        $select = $this->_model->select()->where(new Kwf_Model_Select_Expr_Contains('foo', 'a\'aa'));
        $this->assertEquals("SELECT \"testtable\".* FROM \"testtable\" WHERE (testtable.foo LIKE '%a\'aa%')",
            $this->_model->createDbSelect($select)->__toString());

        $select = $this->_model->select()->where(new Kwf_Model_Select_Expr_Contains('foo', 'a_aa'));
        $this->assertEquals("SELECT \"testtable\".* FROM \"testtable\" WHERE (testtable.foo LIKE '%a\_aa%')",
            $this->_model->createDbSelect($select)->__toString());
    }

    public function testExprLike()
    {
        $select = $this->_model->select()->where(new Kwf_Model_Select_Expr_Like('foo', 'aaa'));
        $this->assertEquals("SELECT \"testtable\".* FROM \"testtable\" WHERE (testtable.foo LIKE 'aaa')",
            $this->_model->createDbSelect($select)->__toString());

        $select = $this->_model->select()->where(new Kwf_Model_Select_Expr_Like('foo', 'aa%a'));
        $this->assertEquals("SELECT \"testtable\".* FROM \"testtable\" WHERE (testtable.foo LIKE 'aa%a')",
            $this->_model->createDbSelect($select)->__toString());

        $select = $this->_model->select()->where(new Kwf_Model_Select_Expr_Like('foo', '%a_aa'));
        $this->assertEquals("SELECT \"testtable\".* FROM \"testtable\" WHERE (testtable.foo LIKE '%a\_aa')",
            $this->_model->createDbSelect($select)->__toString());
    }

    public function testExprStartsWith()
    {
        $select = $this->_model->select()->where(new Kwf_Model_Select_Expr_StartsWith('foo', 'aaa'));
        $this->assertEquals("SELECT \"testtable\".* FROM \"testtable\" WHERE (testtable.foo LIKE 'aaa%')",
            $this->_model->createDbSelect($select)->__toString());
    }

    public function testExprStartsWithEscaping1()
    {
        $select = $this->_model->select()->where(new Kwf_Model_Select_Expr_StartsWith('foo', 'a\'aa'));
        $this->assertEquals("SELECT \"testtable\".* FROM \"testtable\" WHERE (testtable.foo LIKE 'a\\'aa%')",
            $this->_model->createDbSelect($select)->__toString());
    }

    public function testExprStartsWithEscaping2()
    {
        $select = $this->_model->select()->where(new Kwf_Model_Select_Expr_StartsWith('foo', 'a%aa'));
        $this->assertEquals("SELECT \"testtable\".* FROM \"testtable\" WHERE (testtable.foo LIKE 'a\\\\%aa%')",
            $this->_model->createDbSelect($select)->__toString());
    }

    public function testExprStartsWithEscaping3()
    {
        $select = $this->_model->select()->where(new Kwf_Model_Select_Expr_StartsWith('foo', 'a_aa'));
        $this->assertEquals("SELECT \"testtable\".* FROM \"testtable\" WHERE (testtable.foo LIKE 'a\\\\_aa%')",
            $this->_model->createDbSelect($select)->__toString());
    }
    public function testExprSmallerDate()
    {
        $select = $this->_model->select()->where(new Kwf_Model_Select_Expr_Lower('foo', new Kwf_Date('2008-06-06')));
        $this->assertEquals("SELECT \"testtable\".* FROM \"testtable\" WHERE (testtable.foo < '2008-06-06')",
            $this->_model->createDbSelect($select)->__toString());
    }

    public function testExprHigherDate()
    {
        $select = $this->_model->select()->where(new Kwf_Model_Select_Expr_Higher('foo', new Kwf_Date('2008-06-06')));
        $this->assertEquals("SELECT \"testtable\".* FROM \"testtable\" WHERE (testtable.foo > '2008-06-06')",
            $this->_model->createDbSelect($select)->__toString());
    }

    public function testExprOr()
    {
        $expr = new Kwf_Model_Select_Expr_Or(array(
            new Kwf_Model_Select_Expr_Equal('foo', 'aaa'),
            new Kwf_Model_Select_Expr_Equal('foo', 'bbb')

        ));
        $select = $this->_model->select()->where($expr);
        $this->assertEquals("SELECT \"testtable\".* FROM \"testtable\" WHERE ((testtable.foo = 'aaa') OR (testtable.foo = 'bbb'))",
            $this->_model->createDbSelect($select)->__toString());
    }

    public function testExprAnd()
    {
        $expr = new Kwf_Model_Select_Expr_And(array(
            new Kwf_Model_Select_Expr_Equal('foo', 'aaa'),
            new Kwf_Model_Select_Expr_Equal('foo', 'bbb')

        ));
        $select = $this->_model->select()->where($expr);
        $this->assertEquals("SELECT \"testtable\".* FROM \"testtable\" WHERE ((testtable.foo = 'aaa') AND (testtable.foo = 'bbb'))",
            $this->_model->createDbSelect($select)->__toString());
    }

    public function testExprNot()
    {
        $expr = new Kwf_Model_Select_Expr_Not(new Kwf_Model_Select_Expr_Equal('foo', 'aaa'));
        $select = $this->_model->select()->where($expr);
        $this->assertEquals("SELECT \"testtable\".* FROM \"testtable\" WHERE (NOT (testtable.foo = 'aaa'))",
            $this->_model->createDbSelect($select)->__toString());
    }

    public function testExprAndException()
    {
        $this->setExpectedException('Kwf_Exception');
        $expr = new Kwf_Model_Select_Expr_And(array());
        $select = $this->_model->select()->where($expr);
        $this->_model->createDbSelect($select);
    }

    public function testBigExpr()
    {
        $expr = new Kwf_Model_Select_Expr_And(array(
            new Kwf_Model_Select_Expr_Equal('foo', 'aaa'),
            new Kwf_Model_Select_Expr_Equal('foo', 'bbb')

        ));
        $expr2 = new Kwf_Model_Select_Expr_Or(array(
            new Kwf_Model_Select_Expr_Equal('foo', 'aaa'),
            new Kwf_Model_Select_Expr_Equal('foo', 'bbb')

        ));
        $expr3 = new Kwf_Model_Select_Expr_Not(new Kwf_Model_Select_Expr_Equal('foo', 'aaa'));
        $expr4 = new Kwf_Model_Select_Expr_Or(array($expr, $expr2, $expr3));

        $select = $this->_model->select()->where($expr4);
        $this->assertEquals("SELECT \"testtable\".* FROM \"testtable\" WHERE (((testtable.foo = 'aaa') AND ".
         "(testtable.foo = 'bbb')) OR ((testtable.foo = 'aaa') OR (testtable.foo = 'bbb')) OR (NOT (testtable.foo = 'aaa')))",
            $this->_model->createDbSelect($select)->__toString());

    }

    public function testExprEqualExpr()
    {
        $expr = new Kwf_Model_Select_Expr_Equals('foo', new Kwf_Model_Select_Expr_Field('bar'));
        $select = $this->_model->select()->where($expr);
        $sql = $this->_model->createDbSelect($select)->__toString($select);
        $this->assertEquals("SELECT \"testtable\".* FROM \"testtable\" WHERE (testtable.foo = testtable.bar)", $sql);
    }

    public function testExprHigherExpr()
    {
        $expr = new Kwf_Model_Select_Expr_HigherEqual('foo', new Kwf_Model_Select_Expr_Field('bar'));
        $select = $this->_model->select()->where($expr);
        $sql = $this->_model->createDbSelect($select)->__toString($select);
        $this->assertEquals("SELECT \"testtable\".* FROM \"testtable\" WHERE (testtable.foo >= testtable.bar)", $sql);
    }
}
