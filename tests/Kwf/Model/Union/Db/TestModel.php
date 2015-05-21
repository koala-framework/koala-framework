<?php
class Kwf_Model_Union_Db_TestModel extends Kwf_Model_Union
{
    protected $_columnMapping = 'Kwf_Model_Union_Db_TestMapping';
    protected $_models = array(
        '1m' => 'Kwf_Model_Union_Db_Model1',
        '2m' => 'Kwf_Model_Union_Db_Model2',
    );
    protected $_siblingModels = array('Kwf_Model_Union_Db_ModelSibling');
}
