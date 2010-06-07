<?php
class Vps_Form_MultiCheckbox_RelationModelNoRel extends Vps_Model_FnF
{
    protected $_referenceMap = array(
        'Data' => array(
            'column' => 'data_id',
            'refModelClass' => 'Vps_Form_MultiCheckbox_DataModelNoRel'
        ),
        'Value' => array(
            'column' => 'values_id',
            'refModelClass' => 'Vps_Form_MultiCheckbox_ValueModel'
        )
    );}
