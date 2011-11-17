<?php
class Kwc_Columns_ColumnsInColumns_Columns_ColumnsTestModel extends Kwf_Model_FnF
{
    protected $_data = array(
        //testFixedWidth
        array('id' => 1, 'component_id'=>'1-1', 'pos' => 1, 'visible' => true, 'width'=>'90'),
        array('id' => 2, 'component_id'=>'1-1', 'pos' => 2, 'visible' => true, 'width'=>'10'),
        array('id' => 3, 'component_id'=>'1-1-1-2', 'pos' => 1, 'visible' => true, 'width'=>'70'),
        array('id' => 4, 'component_id'=>'1-1-1-2', 'pos' => 2, 'visible' => true, 'width'=>'20'),

        //testPercentageWidth
        array('id' => 10, 'component_id'=>'2-3', 'pos' => 1, 'visible' => true, 'width'=>'90%'),
        array('id' => 11, 'component_id'=>'2-3', 'pos' => 2, 'visible' => true, 'width'=>'10%'),
        array('id' => 12, 'component_id'=>'2-3-10-4', 'pos' => 1, 'visible' => true, 'width'=>'70%'),
        array('id' => 13, 'component_id'=>'2-3-10-4', 'pos' => 2, 'visible' => true, 'width'=>'20%'),
        array('id' => 14, 'component_id'=>'2-3-10-4', 'pos' => 3, 'visible' => true, 'width'=>''),

        //testBoxChangesContent
        array('id' => 15, 'component_id'=>'3-5', 'pos' => 1, 'visible' => true, 'width'=>'80%'),
    );
}
