<?php
class Kwf_Model_Union_DeletedFlag_TestModel extends Kwf_Model_Union
{
    protected $_columnMapping = 'Kwf_Model_Union_DeletedFlag_TestMapping';
    protected $_models = array(
        '1m' => 'Kwf_Model_Union_DeletedFlag_Model1',
        '2m' => 'Kwf_Model_Union_DeletedFlag_Model2',
    );
}
