<?php
class Vpc_Formular_Textarea_IndexController extends Vps_Controller_Action_Auto_Form_Vpc
{
    protected $_fields = array(
            array('type'       => 'TextField',
                  'fieldLabel' => 'Spalten:',
                  'name'       => 'cols',
                  'width'      => 50),
            array('type'       => 'TextField',
                  'fieldLabel' => 'Zeilen',
                  'name'       => 'rows',
                  'width'      => 50),
            );

    protected $_buttons = array('save'   => true);
    protected $_tableName = 'Vpc_Formular_Textarea_IndexModel';
}
