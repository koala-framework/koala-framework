<?php
class Vps_Form_Cards_BarModel extends Vps_Model_FnF
{
    protected $_primaryKey = 'test_id';
    protected $_data = array(
        array('test_id' => 1, 'firstname' => 'Max', 'lastname' =>  'bar'),
        array('test_id' => 2, 'firstname' => 'Susi', 'lastname' =>  'bar')
    );

    protected $_referenceMap = array(
        'Vps_Form_Cards_TopModel' => array(
            'column' => 'test_id',
            'refModelClass' => 'Vps_Form_Cards_TopModel'
        )
    );
}