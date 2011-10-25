<?php
class Kwf_Form_Cards_FooModel extends Kwf_Model_Session
{
    protected $_namespace = 'Kwf_Form_Cards_FooModel';
    protected $_primaryKey = 'test_id';
    protected $_defaultData = array(
        array('test_id' => 3, 'firstname' => 'Max', 'lastname' =>  'foo', 'job' => 'Tischler'),
        array('test_id' => 4, 'firstname' => 'Susi', 'lastname' =>  'foo', 'job' => 'Sekretariat')
    );

    protected $_referenceMap = array(
        'Kwf_Form_Cards_TopModel' => array(
            'column' => 'test_id',
            'refModelClass' => 'Kwf_Form_Cards_TopModel'
        )
    );
}