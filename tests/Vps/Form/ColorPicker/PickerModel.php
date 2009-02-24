<?php
class Vps_Form_ColorPicker_PickerModel extends Vps_Model_FnF
{
    protected $_namespace = 'Vps_Form_ColorPicker_PickerModel';
    protected $_primaryKey = 'test_id';
    protected $_defaultData = array(
        array('id' => 3, 'hex' => '#555555'),
        array('id' => 4, 'hex' => '#005000')
    );
}