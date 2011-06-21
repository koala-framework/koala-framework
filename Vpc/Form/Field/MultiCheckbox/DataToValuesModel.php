<?php
class Vpc_Form_Field_MultiCheckbox_DataToValuesModel extends Vps_Model_FnF
{
    protected $_referenceMap = array(
        'Data' => array(
            'column' => 'data_id',
            'refModelClass' => 'Vps_Model_Mail' //TODO das ist nicht korrekt
        ),
        'Value' => array(
            'column' => 'value_id',
            'refModelClass' => 'Vpc_Form_Field_MultiCheckbox_ValuesModel'
        ),
    );
}
