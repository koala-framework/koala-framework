<?php
class Vps_Filter_Row_SkipFilterTestFilter extends Vps_Filter_Row_AutoFill
{
    public function skipFilter($row)
    {
        return !!$row->skip;
    }
}
class Vps_Filter_Row_SkipFilterTest extends Vps_Test_TestCase
{
    public function testSkipFilter()
    {
        $model = new Vps_Model_FnF(array('data'=>array(
        ), 'filters'=>array('test'=>new Vps_Filter_Row_AutoFill('abc'))));

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
