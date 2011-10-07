<?php
class Kwc_Form_Field_MultiCheckbox_DataToValuesModel extends Kwf_Model_FnF
{
    protected $_referenceMap = array(
        'Data' => array(
            'column' => 'data_id',
            'refModelClass' => 'Kwf_Model_Mail' //TODO das ist nicht korrekt
        ),
        'Value' => array(
            'column' => 'value_id',
            'refModelClass' => 'Kwc_Form_Field_MultiCheckbox_ValuesModel'
        ),
    );
}
