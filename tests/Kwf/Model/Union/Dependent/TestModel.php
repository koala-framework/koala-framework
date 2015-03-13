<?php
class Kwf_Model_Union_Dependent_TestModel extends Kwf_Model_Union
{
    protected $_columnMapping = 'Kwf_Model_Union_Dependent_TestMapping';
    protected $_models = array(
        '1m' => 'Kwf_Model_Union_Dependent_Model1',
        '2m' => 'Kwf_Model_Union_Dependent_Model2',
    );

    protected function _init()
    {
        $this->_referenceMap['Parent'] = 'parent_id->Kwf_Model_Union_Dependent_Parent';
        parent::_init();
    }
}
