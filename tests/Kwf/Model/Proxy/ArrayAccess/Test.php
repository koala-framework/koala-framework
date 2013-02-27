<?php
class Kwf_Model_Proxy_ArrayAccess_Test extends Kwf_Test_TestCase
{
    protected function _initFnF()
    {
        $fnf = new Kwf_Model_FnF(array(
            'columns' => array('id', 'value'),
            'data' => array(
                array('id' => 1, 'value' => 'value1'),
                array('id' => 2, 'value' => 'value2'),
                array('id' => 3, 'value' => 'Peter Griffin'),
                array('id' => 4, 'value' => 'value4'),
                array('id' => 5, 'value' => 'value5')
            )
        ));
        return new Kwf_Model_Proxy(array('proxyModel' => $fnf));
    }
    public function testOffsetGet()
    {
        $model = $this->_initFnF();

        $rows = $model->getRows();

        $row = $rows[0];
        $this->assertEquals($row->id, 1);
        $this->assertEquals($row->value, 'value1');

        $row = $rows[4];
        $this->assertEquals($row->id, 5);
        $this->assertEquals($row->value, 'value5');

        $row = $rows[2];
        $this->assertEquals($row->id, 3);
        $this->assertEquals($row->value, 'Peter Griffin');
    }
    public function testOffsetExists()
    {
        $model = $this->_initFnF();
        $rows = $model->getRows();

        $isRowSet = isset($rows[0]);
        $this->assertEquals($isRowSet, true);

        $isRowSet = isset($rows[4]);
        $this->assertEquals($isRowSet, true);

        $isRowSet = isset($rows[3]);
        $this->assertEquals($isRowSet, true);

        $isRowSet = isset($rows[-3]);
        $this->assertEquals($isRowSet, false);

        $isRowSet = isset($rows[5]);
        $this->assertEquals($isRowSet, false);
    }
}
