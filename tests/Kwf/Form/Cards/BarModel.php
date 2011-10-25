<?php
class Kwf_Form_Cards_BarModel extends Kwf_Model_Session
{
    protected $_namespace = 'Kwf_Form_Cards_BarModel';
    protected $_primaryKey = 'test_id';
    protected $_defaultData = array(
        array('test_id' => 1, 'firstname' => 'Max', 'lastname' =>  'bar'),
        array('test_id' => 2, 'firstname' => 'Susi', 'lastname' =>  'bar')
    );

    protected $_referenceMap = array(
        'Kwf_Form_Cards_TopModel' => array(
            'column' => 'test_id',
            'refModelClass' => 'Kwf_Form_Cards_TopModel'
        )
    );
}