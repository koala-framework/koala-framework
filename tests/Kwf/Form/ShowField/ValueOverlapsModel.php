<?php
class Kwf_Form_ShowField_ValueOverlapsModel extends Kwf_Model_FnF
{
    protected $_namespace = 'testShowFieldValueOverlabs';
    protected $_data = array(
        array('id' => 1, 'firstname' => 'Max', 'lastname' =>  'Musterman', 'job' => 'Tischler'),
        array('id' => 2, 'firstname' => 'Susi', 'lastname' =>  'Musterfrau', 'job' => 'Sekretariat')
    );
}