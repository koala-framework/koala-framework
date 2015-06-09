<?php
class Kwf_Model_Union_FnF_Model2 extends Kwf_Model_FnF
{
    protected $_columnMappings = array(
        'Kwf_Model_Union_FnF_TestMapping' => array(
            'foo' => 'aa',
            'bar' => 'bb',
            'baz' => null,
        )
    );
    protected $_data = array(
        array('id' => 1, 'aa' => 'xx', 'bb' => 'xx1'),
        array('id' => 2, 'aa' => '333', 'bb' => 'yy1'),
        array('id' => 3, 'aa' => 'zz', 'bb' => 'zz1'),
    );
}
