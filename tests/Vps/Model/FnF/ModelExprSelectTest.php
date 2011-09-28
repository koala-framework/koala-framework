<?php
/**
 * @group Model
 * @group Model_FnF
 */
class Vps_Model_FnF_ModelExprSelectTest extends Vps_Test_TestCase
{
    public function testExprNullSelect()
    {
        $model = new Vps_Model_FnF();
        $model->setData(array(
            array('id' => 1, 'value' => 'foo'),
            array('id' => 2, 'value' => null),
            array('id' => 3),
        ));

        $select = $model->select();
        $select->where(new Vps_Model_Select_Expr_IsNull('value'));
        $rows = $model->getRows($select);
        $count = $rows->count();
        $current = $rows->current();
        $this->assertEquals(2, $count);
        $this->assertEquals(2, $current->id);
    }

    public function testExprEqualsInSelect()
    {
        $model = new Vps_Model_FnF();
        $model->setData(array(
            array('id' => 1, 'value' => 'foo'),
            array('id' => 2, 'value' => 'bar'),
        ));

        $select = $model->select();
        $select->where(new Vps_Model_Select_Expr_Equals('value', array('bar', 'foo')));
        $rows = $model->getRows($select);
        $count = $rows->count();
        $current = $rows->current();
        $this->assertEquals(2, $count);
        $this->assertEquals(1, $current->id);

        $this->assertEquals(1, $model->getRows($model->select()
                                ->where(new Vps_Model_Select_Expr_Equals('value', 'foo')))->current()->id);
    }

    public function testExprEqualsSelect()
    {
        $model = new Vps_Model_FnF();
        $model->setData(array(
            array('id' => 1, 'value' => 'foo'),
            array('id' => 2, 'value' => 'bar'),
        ));

        $select = $model->select();
        $select->where(new Vps_Model_Select_Expr_Equals('value', 'bar'));
        $rows = $model->getRows($select);
        $count = $rows->count();
        $current = $rows->current();
        $this->assertEquals(1, $count);
        $this->assertEquals(2, $current->id);

        $this->assertEquals(1, $model->getRows($model->select()
                                ->where(new Vps_Model_Select_Expr_Equals('value', 'foo')))->current()->id);
    }

    public function testExprHigherSelect()
    {
        $model = new Vps_Model_FnF();
        $model->setData(array(
            array('id' => 1, 'value' => 5),
            array('id' => 2, 'value' => 8),
        ));

        $select = $model->select();
        $select->where(new Vps_Model_Select_Expr_Higher('value', 6));
        $rows = $model->getRows($select);
        $count = $rows->count();
        $current = $rows->current();
        $this->assertEquals(1, $count);
        $this->assertEquals(2, $current->id);
    }

    public function testExprHigherAndEqualSelect()
    {
        $model = new Vps_Model_FnF();
        $model->setData(array(
            array('id' => 1, 'value' => 5),
            array('id' => 2, 'value' => 8),
        ));

        $select = $model->select();

        $select->where(new Vps_Model_Select_Expr_Higher('value', 6));
        $select->where(new Vps_Model_Select_Expr_Equals('value', 7));

        $rows = $model->getRows($select);
        $count = $rows->count();
        $this->assertEquals(0, $count);

        $select = $model->select();
        $select->where(new Vps_Model_Select_Expr_Higher('value', 6));
        $select->whereEquals('value', 8);

        $rows = $model->getRows($select);
        $count = $rows->count();
        $this->assertEquals(1, $count);
    }

    public function testExprOrSelect()
    {
        $model = new Vps_Model_FnF();
        $model->setData(array(
            array('id' => 1, 'value' => 5),
            array('id' => 2, 'value' => 8),
            array('id' => 3, 'value' => 3),
        ));

        $select = $model->select();
        $orExpression = new Vps_Model_Select_Expr_Or(array(new Vps_Model_Select_Expr_Higher('value', 6),
                                                           new Vps_Model_Select_Expr_Equals('value', 5)));
        $select->where($orExpression);
        $rows = $model->getRows($select);
        $count = $rows->count();
        $current = $rows->current();
        $this->assertEquals(2, $count);
    }

    public function testExprNotSelect()
    {
        $model = new Vps_Model_FnF();
        $model->setData(array(
            array('id' => 1, 'value' => 5),
            array('id' => 2, 'value' => 8),
            array('id' => 3, 'value' => 3),
        ));

        $select = $model->select();
        $notExpression = new Vps_Model_Select_Expr_Not(new Vps_Model_Select_Expr_Higher('value', 6));
        $select->where($notExpression);
        $rows = $model->getRows($select);
        $count = $rows->count();
        $current = $rows->current();
        $this->assertEquals(2, $count);
        $this->assertEquals(1, $current->id);
    }

    public function testExprSmaller()
    {
        $model = new Vps_Model_FnF();
        $model->setData(array(
            array('id' => 1, 'value' => 5),
            array('id' => 2, 'value' => 8),
            array('id' => 3, 'value' => 3),
            array('id' => 4, 'value' => 10),
            array('id' => 5, 'value' => 1),
            array('id' => 6, 'value' => 13),
            array('id' => 7, 'value' => 15),
        ));

        $select = $model->select();
        $smallExpression = new Vps_Model_Select_Expr_Smaller('value', 13);

        $select = $model->select();
        $select->where($smallExpression);
        $rows = $model->getRows($select);
        $count = $rows->count();
        $this->assertEquals(5, $count);
    }

    public function testExprContains()
    {
        $model = new Vps_Model_FnF();
        $model->setData(array(
            array('id' => 1, 'value' => 'Herbert'),
            array('id' => 2, 'value' => 'Kurt'),
            array('id' => 3, 'value' => 'Klaus'),
            array('id' => 4, 'value' => 'Rainer'),
            array('id' => 5, 'value' => 'Franz'),
            array('id' => 6, 'value' => 'Niko'),
            array('id' => 7, 'value' => 'Lorenz'),
        ));

        $select = $model->select();
        $containsExpression = new Vps_Model_Select_Expr_Contains('value', 'Kla');
        $select = $model->select();
        $select->where($containsExpression);
        $rows = $model->getRows($select);
        $count = $rows->count();
        $this->assertEquals(1, $count);

        $containsExpression = new Vps_Model_Select_Expr_Contains('value', 'n');
        $select = $model->select();
        $select->where($containsExpression);
        $rows = $model->getRows($select);
        $count = $rows->count();
        $this->assertEquals(4, $count);
    }

    public function testExprLike()
    {
        $model = new Vps_Model_FnF();
        $model->setData(array(
            array('id' => 1, 'value' => 'aaa'),
            array('id' => 2, 'value' => 'aba'),
            array('id' => 3, 'value' => 'caa'),
            array('id' => 3, 'value' => 'aaa_1'),
        ));

        $rows = $model->getRows($model->select()->where(
            new Vps_Model_Select_Expr_Like('value', 'a%')
        ));
        $this->assertEquals(3, $rows->count());

        $rows = $model->getRows($model->select()->where(
            new Vps_Model_Select_Expr_Like('value', 'a%_%')
        ));
        $this->assertEquals(1, $rows->count());

        $rows = $model->getRows($model->select()->where(
            new Vps_Model_Select_Expr_Like('value', '%c%')
        ));
        $this->assertEquals(1, $rows->count());

        $rows = $model->getRows($model->select()->where(
            new Vps_Model_Select_Expr_Like('value', '%f%')
        ));
        $this->assertEquals(0, $rows->count());
    }

    public function testExprExtraBig()
    {
        $model = new Vps_Model_FnF();
        $model->setData(array(
            array('id' => 1, 'value' => 5),
            array('id' => 2, 'value' => 8),
            array('id' => 3, 'value' => 3),
            array('id' => 4, 'value' => 10),
            array('id' => 5, 'value' => 1),
            array('id' => 6, 'value' => 13),
            array('id' => 7, 'value' => 15),
        ));

        $select = $model->select();
        $orExpression = new Vps_Model_Select_Expr_Or(array(new Vps_Model_Select_Expr_Smaller('value', 8),
                                                           new Vps_Model_Select_Expr_Higher('value', 13)));

        $notExpression = new Vps_Model_Select_Expr_Not($orExpression);
        $select->where($notExpression);
        $rows = $model->getRows($select);
        $count = $rows->count();
        $current = $rows->current();
        $this->assertEquals(3, $count);
    }

    public function testExprStartsWith()
    {
        $model = new Vps_Model_FnF();
        $model->setData(array(
            array('id' => 1, 'value' => 'Herbert'),
            array('id' => 2, 'value' => 'Kurt'),
            array('id' => 3, 'value' => 'Klaus'),
            array('id' => 4, 'value' => 'Rainer'),
            array('id' => 5, 'value' => 'Franz'),
            array('id' => 6, 'value' => 'Niko'),
            array('id' => 7, 'value' => 'Lorenz'),
        ));

        $rows = $model->getRows($model->select()
            ->where(new Vps_Model_Select_Expr_StartsWith('value', 'Kla')));
        $this->assertEquals(1, $rows->count());

        $rows = $model->getRows($model->select()
            ->where(new Vps_Model_Select_Expr_StartsWith('value', 'laus')));
        $this->assertEquals(0, $rows->count());

        $rows = $model->getRows($model->select()
            ->where(new Vps_Model_Select_Expr_StartsWith('value', 'kla')));
        $this->assertEquals(0, $rows->count());
    }
}
