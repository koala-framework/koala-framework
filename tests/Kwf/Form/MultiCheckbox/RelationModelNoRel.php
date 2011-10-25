<?php
class Kwf_Form_MultiCheckbox_RelationModelNoRel extends Kwf_Model_FnF
{
    protected $_referenceMap = array(
        'Data' => array(
            'column' => 'data_id',
            'refModelClass' => 'Kwf_Form_MultiCheckbox_DataModelNoRel'
        ),
        'Value' => array(
            'column' => 'values_id',
            'refModelClass' => 'Kwf_Form_MultiCheckbox_ValueModel'
        )
    );
}
