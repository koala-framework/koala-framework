<?php
/**
 * @group AutoFilter
 */
class Vps_AutoFilter_Test extends PHPUnit_Framework_TestCase
{

    public function testTextFilter()
    {
        $model = new Vps_Model_FnF(array(
            'data' => array(
                array('id' => 1, 'value' => 'foo'),
                array('id' => 2, 'value' => 'foobar'),
                array('id' => 3, 'value' => 'bar'),
            )
        ));
        $filter = new Vps_Controller_Action_Auto_Filter_Text();
        $filter->setModel($model);
        $filter->setQueryFields(array('value'));

        $select = $filter->formatSelect($model->select());
        $this->assertEquals(3, $model->countRows($select));

        $select = $filter->formatSelect($model->select(), array('query' => 'foo'));
        $this->assertEquals(2, $model->countRows($select));

        $select = $filter->formatSelect($model->select(), array('query' => 'foobar'));
        $this->assertEquals(1, $model->countRows($select));

        $select = $filter->formatSelect($model->select(), array('query' => 'foo bar'));
        $this->assertEquals(1, $model->countRows($select));

        $select = $filter->formatSelect($model->select(), array('query' => 'xxx yyy'));
        $this->assertEquals(0, $model->countRows($select));

        $select = $filter->formatSelect($model->select(), array('query' => 'value:foo'));
        $this->assertEquals(2, $model->countRows($select));

        $select = $filter->formatSelect($model->select(), array('query' => 'value: foo'));
        $this->assertEquals(2, $model->countRows($select));

        $select = $filter->formatSelect($model->select(), array('query' => 'id:2'));
        $this->assertEquals(1, $model->countRows($select));
        $this->assertEquals(2, $model->getRows($select)->current()->id);
    }

    public function testQueryFilter()
    {
        $model = new Vps_Model_FnF(array(
            'data' => array(
                array('id' => 1, 'value' => 'foo'),
                array('id' => 2, 'value' => 'foobar'),
                array('id' => 3, 'value' => 'bar'),
            )
        ));
        $filter = new Vps_Controller_Action_Auto_Filter_Query();
        $filter->setModel($model);
        $filter->setFieldName('value');

        $select = $filter->formatSelect($model->select());
        $this->assertEquals(3, $model->countRows($select));

        $select = $filter->formatSelect($model->select(), array('query_value' => 'foo'));
        $this->assertEquals(1, $model->countRows($select));

        $select = $filter->formatSelect($model->select(), array('query_value' => 'foobar'));
        $this->assertEquals(1, $model->countRows($select));

        $select = $filter->formatSelect($model->select(), array('query_value' => 'xxx'));
        $this->assertEquals(0, $model->countRows($select));

        $filter->setSelectType(Vps_Controller_Action_Auto_Filter_Query::SELECT_TYPE_CONTAINS);
        $select = $filter->formatSelect($model->select(), array('query_value' => 'foo'));
        $this->assertEquals(2, $model->countRows($select));
        $select = $filter->formatSelect($model->select(), array('query_value' => 'bar'));
        $this->assertEquals(2, $model->countRows($select));
        $select = $filter->formatSelect($model->select(), array('query_value' => 'foobar'));
        $this->assertEquals(1, $model->countRows($select));
    }

    public function testDateRangeFilter()
    {
        $model = new Vps_Model_FnF(array(
            'data' => array(
                array('id' => 1, 'date' => '2010-05-01'),
                array('id' => 2, 'date' => '2010-05-02'),
                array('id' => 3, 'date' => '2010-05-03'),
            )
        ));
        $filter = new Vps_Controller_Action_Auto_Filter_DateRange();
        $filter->setModel($model);
        $filter->setFieldName('date');

        $select = $filter->formatSelect($model->select());
        $this->assertEquals(3, $model->countRows($select));

        $select = $filter->formatSelect($model->select(),
            array('date_from' => '2010-05-02')
        );
        $this->assertEquals(2, $model->countRows($select));

        $select = $filter->formatSelect($model->select(),
            array('date_to' => '2010-05-01')
        );
        $this->assertEquals(1, $model->countRows($select));

        $select = $filter->formatSelect($model->select(),
            array('date_from' => '2010-05-02', 'date_to' => '2010-05-02')
        );
        $this->assertEquals(1, $model->countRows($select));

        $select = $filter->formatSelect($model->select(),
            array('date_from' => '2010-05-01', 'date_to' => '2010-05-03')
        );
        $this->assertEquals(3, $model->countRows($select));

        $select = $filter->formatSelect($model->select(),
            array('date_from' => '2010-05-04', 'date_to' => '2010-05-01')
        );
        $this->assertEquals(1, $model->countRows($select));

        $filter = new Vps_Controller_Action_Auto_Filter_DateRange();
        $filter->setModel($model);
        $filter->setFieldName('date');
        $filter->setFrom('2010-05-02');

        $select = $filter->formatSelect($model->select());
        $this->assertEquals(2, $model->countRows($select));

        $select = $filter->formatSelect($model->select(),
            array('date_from' => '2010-05-01', 'date_to' => '2010-05-03')
        );
        $this->assertEquals(3, $model->countRows($select));
    }
}