<?php
class Kwf_Model_Union_FnF_TestModel extends Kwf_Model_Union
{
    protected $_columnMapping = 'Kwf_Model_Union_FnF_TestMapping';
    protected $_models = array(
        '1m' => 'Kwf_Model_Union_FnF_Model1',
        '2m' => 'Kwf_Model_Union_FnF_Model2',
    );
    protected $_siblingModels = array('Kwf_Model_Union_FnF_ModelSibling');
}
