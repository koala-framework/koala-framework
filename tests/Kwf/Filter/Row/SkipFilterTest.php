<?php
class Kwf_Filter_Row_SkipFilterTestFilter extends Kwf_Filter_Row_AutoFill
{
    public function skipFilter($row, $column)
    {
        return !!$row->skip;
    }
}
class Kwf_Filter_Row_SkipFilterTest extends Kwf_Test_TestCase
{
    public function testSkipFilter()
    {
        $model = new Kwf_Model_FnF(array('data'=>array(
        ), 'filters'=>array('test'=>new Kwf_Filter_Row_AutoFill('abc'))));

        $row = $model->createRow();
        $row->foo = 'foo4';
        $row->skip = 0;
        $row->test = 'bam';
        $row->save();
        $this->assertEquals('abc', $model->getRow(1)->test);

        $row->skip = 1;
        $row->save();
        $row->test = 'bam';
        $this->assertEquals('bam', $model->getRow(1)->test);
    }
}
