<?php
abstract class Kwf_Model_Union_Abstract_Test extends Kwf_Test_TestCase
{
    protected $_m;

    public function testSiblingValue()
    {
        $this->assertEquals($this->_m->getRow('1m1')->sib, 's1');
        $this->assertEquals($this->_m->getRow('1m2')->sib, 'ss2');
        $this->assertEquals($this->_m->getRow('2m2')->sib, 'sss3');
        $this->assertEquals(isset($this->_m->getRow('2m2')->sib), true);
    }

    public function testSiblingValueSet()
    {
        $r = $this->_m->getRow('1m1');
        $r->sib = 'foo';
        $r->save();
    }

    public function testCountAll()
    {
        $this->assertEquals(6, $this->_m->countRows());
    }

    public function testCountSelectWhereId()
    {
        $s = new Kwf_Model_Select();
        $s->whereId('1m1');
        $this->assertEquals(1, $this->_m->countRows($s));
    }
    public function testCountSelectWhereEquals()
    {
        $s = new Kwf_Model_Select();
        $s->whereEquals('foo', 'aa');
        $this->assertEquals(1, $this->_m->countRows($s));
    }

    public function testCountSelectWhereNotEquals()
    {
        $s = new Kwf_Model_Select();
        $s->whereNotEquals('foo', 'aa');
        $this->assertEquals(5, $this->_m->countRows($s));
    }

    public function testCountSelectWhereNull()
    {
        $s = new Kwf_Model_Select();
        $s->whereNull('foo');
        $this->assertEquals(0, $this->_m->countRows($s));
    }

    public function testCountSelectWhereExprEquals()
    {
        $s = new Kwf_Model_Select();
        $s->where(new Kwf_Model_Select_Expr_Equal('foo', 'aa'));
        $this->assertEquals(1, $this->_m->countRows($s));
    }

    public function testCountSelectWhereExprNotEquals()
    {
        $s = new Kwf_Model_Select();
        $s->where(new Kwf_Model_Select_Expr_Not(new Kwf_Model_Select_Expr_Equal('foo', 'aa')));
        $this->assertEquals(5, $this->_m->countRows($s));
    }

    public function testCountSelectWhereExprOrEquals()
    {
        $s = new Kwf_Model_Select();
        $s->where(new Kwf_Model_Select_Expr_Or(array(
            new Kwf_Model_Select_Expr_Equal('foo', 'aa'),
            new Kwf_Model_Select_Expr_Equal('foo', 'xx'),
        )));
        $this->assertEquals(2, $this->_m->countRows($s));
    }

    public function testCountSelectWhereEqualsId()
    {
        $s = new Kwf_Model_Select();
        $s->whereEquals('id', '1m1');
        $this->assertEquals(1, $this->_m->countRows($s));
    }

    public function testCountSelectWhereExprEqualsId()
    {
        $s = new Kwf_Model_Select();
        $s->where(new Kwf_Model_Select_Expr_Equal('id', '1m1'));
        $this->assertEquals(1, $this->_m->countRows($s));
    }

    public function testCountSiblingEquals()
    {
        $s = new Kwf_Model_Select();
        $s->whereEquals('sib', 'ss2');
        $this->assertEquals(1, $this->_m->countRows($s));
    }

    public function testCountSiblingExprEquals()
    {
        $s = new Kwf_Model_Select();
        $s->where(new Kwf_Model_Select_Expr_Equal('sib', 'ss2'));
        $this->assertEquals(1, $this->_m->countRows($s));
    }

    public function testGetRowsSelectWhereEquals()
    {
        $s = new Kwf_Model_Select();
        $s->whereEquals('foo', 'aa');
        $rows = $this->_m->getRows($s);
        $this->assertEquals(1, count($rows));
        $this->assertEquals('aa', $rows[0]->foo);
        $this->assertEquals('1m1', $rows[0]->id);
    }

    public function testGetRowsSelectWhereExprEqualsId()
    {
        $s = new Kwf_Model_Select();
        $s->where(new Kwf_Model_Select_Expr_Equal('id', '2m2'));
        $rows = $this->_m->getRows($s);
        $this->assertEquals(1, count($rows));
        $this->assertEquals('2m2', $rows[0]->id);
    }

    public function testGetRowsOrder()
    {
        $s = new Kwf_Model_Select();
        $s->order('foo');
        $rows = $this->_m->getRows($s);
        $this->assertEquals(6, count($rows));
        $this->assertEquals('2', $rows[0]->foo);
        $this->assertEquals('333', $rows[1]->foo);
        $this->assertEquals('aa', $rows[2]->foo);
        $this->assertEquals('aa3', $rows[3]->foo);
        $this->assertEquals('xx', $rows[4]->foo);
        $this->assertEquals('zz', $rows[5]->foo);
    }

    public function testGetRowsSiblingEquals()
    {
        $s = new Kwf_Model_Select();
        $s->whereEquals('sib', 'ss2');
        $rows = $this->_m->getRows($s);
        $this->assertEquals(1, count($rows));
        $this->assertEquals('1m2', $rows[0]->id);
    }

    public function testGetRowsSiblingExprEquals()
    {
        $s = new Kwf_Model_Select();
        $s->where(new Kwf_Model_Select_Expr_Equal('sib', 'ss2'));
        $rows = $this->_m->getRows($s);
        $this->assertEquals(1, count($rows));
        $this->assertEquals('1m2', $rows[0]->id);
    }

    public function testGetIdsOrder()
    {
        $s = new Kwf_Model_Select();
        $s->order('foo');
        $ids = $this->_m->getIds($s);
        $this->assertEquals(6, count($ids));
        $this->assertEquals('1m2', $ids[0]);
        $this->assertEquals('2m2', $ids[1]);
        $this->assertEquals('1m1', $ids[2]);
        $this->assertEquals('1m3', $ids[3]);
        $this->assertEquals('2m1', $ids[4]);
        $this->assertEquals('2m3', $ids[5]);
    }

    public function testGetRowsLimit()
    {
        $s = new Kwf_Model_Select();
        $s->limit(3);
        $rows = $this->_m->getRows($s);
        $this->assertEquals(3, count($rows));

        $s = new Kwf_Model_Select();
        $s->limit(3, 3);
        $rows = $this->_m->getRows($s);
        $this->assertEquals(3, count($rows));
    }

    public function testGetIdsLimit()
    {
        $s = new Kwf_Model_Select();
        $s->limit(3);
        $ids1 = $this->_m->getIds($s);
        $this->assertEquals(3, count($ids1));

        $s = new Kwf_Model_Select();
        $s->limit(3, 3);
        $ids2 = $this->_m->getIds($s);
        $this->assertEquals(3, count($ids2));

        $this->assertTrue(count(array_intersect($ids1, $ids2)) == 0);
    }

    public function testGetIdsOrderLimit()
    {
        $s = new Kwf_Model_Select();
        $s->limit(2);
        $s->order('foo');
        $ids = $this->_m->getIds($s);
        $this->assertEquals(2, count($ids));
        $this->assertEquals('1m2', $ids[0]);
        $this->assertEquals('2m2', $ids[1]);

        $s = new Kwf_Model_Select();
        $s->limit(2, 2);
        $s->order('foo');
        $ids = $this->_m->getIds($s);
        $this->assertEquals('1m1', $ids[0]);
        $this->assertEquals('1m3', $ids[1]);

        $s = new Kwf_Model_Select();
        $s->limit(2, 4);
        $s->order('foo');
        $ids = $this->_m->getIds($s);
        $this->assertEquals('2m1', $ids[0]);
        $this->assertEquals('2m3', $ids[1]);
    }

    public function testGetRowByPrimaryKey()
    {
        $row = $this->_m->getRow('1m2');
        $this->assertEquals('1m2', $row->id);
        $this->assertEquals('2', $row->foo);
    }

    public function testChangeRow()
    {
        $row = $this->_m->getRow('1m2');
        $row->foo = 'bar';
        $row->save();

        Kwf_Model_Abstract::clearAllRows();

        $models = $this->_m->getUnionModels();
        $row = $models['1m']->getRow(2);
        $this->assertEquals('bar', $row->foo);

        $s = new Kwf_Model_Select();
        $s->whereEquals('foo', 'bar');
        $this->assertEquals(1, $this->_m->countRows($s));
    }

    public function testDeleteRow()
    {
        $row = $this->_m->getRow('1m2');
        $row->delete();

        Kwf_Model_Abstract::clearAllRows();

        $models = $this->_m->getUnionModels();
        $this->assertEquals(2, $models['1m']->countRows(array()));

        $s = new Kwf_Model_Select();
        $this->assertEquals(5, $this->_m->countRows($s));
    }

    public function testCreateRow()
    {
        return; //not yet implemented
        $row = $this->_m->createRow();
        $row->model = '1m'; //is that the only & best way to select the desired target model?
        $row->save();

        Kwf_Model_Abstract::clearAllRows();

        $models = $this->_m->getUnionModels();
        $this->assertEquals(4, $models['1m']->countRows(array()));

        $s = new Kwf_Model_Select();
        $this->assertEquals(7, $this->_m->countRows($s));
    }

    public function testEmptyMappingColumn1()
    {
        $s = new Kwf_Model_Select();
        $s->where(new Kwf_Model_Select_Expr_Equal('baz', 'cc'));
        $this->assertEquals(1, $this->_m->countRows($s));
    }

    public function testEmptyMappingColumn2()
    {
        $s = new Kwf_Model_Select();
        $s->where(new Kwf_Model_Select_Expr_And(array(
            new Kwf_Model_Select_Expr_Equal('foo', 'aa'),
            new Kwf_Model_Select_Expr_Equal('baz', 'cc')
        )));
        $this->assertEquals(1, $this->_m->countRows($s));

    }

    public function testEmptyMappingColumn3()
    {
        $s = new Kwf_Model_Select();
        $s->where(new Kwf_Model_Select_Expr_Or(array(
            new Kwf_Model_Select_Expr_Equal('baz', 'cc'),
            new Kwf_Model_Select_Expr_Equal('baz', 'cc3')
        )));
        $this->assertEquals(2, $this->_m->countRows($s));
    }
}
