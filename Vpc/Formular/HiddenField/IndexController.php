<?php
class Vpc_Formular_HiddenField_IndexController extends Vps_Controller_Action_Auto_Form_Vpc
{
    protected $_fields = array(
            array('type'       => 'TextField',
                  'fieldLabel' => 'Name',
                  'name'       => 'name'),
                  );

    protected $_buttons = array('save'   => true);
    protected $_tableName = 'Vpc_Formular_HiddenField_IndexModel';
    protected $_primaryKey = 'id';
       
}
