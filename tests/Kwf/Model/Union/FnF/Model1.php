<?php
class Kwf_Model_Union_FnF_Model1 extends Kwf_Model_FnF
{
    protected $_columnMappings = array(
        'Kwf_Model_Union_FnF_TestMapping' => array(
            'foo' => 'foo',
            'bar' => 'bar',
            'baz' => 'baz',
        )
    );
    protected $_data = array(
        array('id' => 1, 'foo' => 'aa', 'bar' => 'bb', 'baz' => 'cc'),
        array('id' => 2, 'foo' => '2', 'bar' => '2', 'baz' => '2'),
        array('id' => 3, 'foo' => 'aa3', 'bar' => 'bb3', 'baz' => 'cc3'),
    );
}
