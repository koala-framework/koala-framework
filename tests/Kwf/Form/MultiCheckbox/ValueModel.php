<?php
class Kwf_Form_MultiCheckbox_ValueModel extends Kwf_Model_FnF
{
    public $_data = array(
        array('id' => 1, 'value' => 'Value 1'),
        array('id' => 2, 'value' => 'Value 2'),
        array('id' => 3, 'value' => 'Value 3')
    );
    protected $_toStringField = 'value';
    protected $_dependentModels = array(
        'Relation' => 'Kwf_Form_MultiCheckbox_RelationModel'
    );
}
