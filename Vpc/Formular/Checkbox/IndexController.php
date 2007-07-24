<?php
class Vpc_Formular_Checkbox_IndexController extends Vps_Controller_Action_Auto_Form_Vpc
{
    protected $_fields = array(
            array('type'       => 'TextField',
                  'fieldLabel' => 'Text',
                  'name'       => 'text',
                  'width'      => 50),
            array('type'       => 'TextField',
                  'fieldLabel' => 'Value',
                  'name'       => 'value',
                  'width'      => 50),
            array('type'       => 'TextField',
                  'fieldLabel' => 'Checked',
                  'name'       => 'checked',
                  'width'      => 50),
    );
    protected $_buttons = array('save'   => true);
    protected $_tableName = 'Vpc_Formular_Checkbox_IndexModel';
    protected $_primaryKey = 'id';
}



