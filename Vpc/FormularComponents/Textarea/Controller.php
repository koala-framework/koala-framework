<?php
class Vpc_Formular_Textarea_Controller extends Vps_Controller_Action_Auto_Vpc_Form
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
    protected $_tableName = 'Vpc_Formular_Textarea_Model';
}
