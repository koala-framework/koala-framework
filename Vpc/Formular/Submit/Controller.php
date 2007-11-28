<?php
class Vpc_Formular_Submit_Controller extends Vps_Controller_Action_Auto_Vpc_Form
{
    protected $_fields = array(
            array('type'       => 'TextField',
                  'fieldLabel' => 'Text',
                  'name'       => 'text',
                  'width'      => 150),
            );

    protected $_buttons = array('save'   => true);
    protected $_tableName = 'Vpc_Formular_Submit_Model';
}