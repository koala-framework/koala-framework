<?php
class Vpc_Formular_Password_IndexController extends Vps_Controller_Action_Auto_Form_Vpc
{
    protected $_fields = array(
            array('type'       => 'TextField',
                  'fieldLabel' => 'Breite (in Pixel)',
                  'name'       => 'width',
                  'width'      => 50),
            array('type'       => 'TextField',
                  'fieldLabel' => 'Maximale Textlänge',
                  'name'       => 'maxlength',
                  'width'      => 50),
            );

    protected $_buttons = array('save'   => true);
    protected $_tableName = 'Vpc_Formular_Password_IndexModel';
    protected $_primaryKey = 'id';
}
