<?php
class Vpc_Formular_Email_IndexController extends Vps_Controller_Action_Auto_Form_Vpc
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
            array('type'       => 'TextField',
                  'fieldLabel' => 'Standardwert',
                  'name'       => 'value',
                  'width'      => 150),
    );

    protected $_buttons = array('save'   => true);
    protected $_tableName = 'Vpc_Formular_Email_IndexModel';
    protected $_primaryKey = 'id';

}
