<?php
class Vpc_Formular_Textbox_IndexController extends Vps_Controller_Action_Auto_Form_Vpc
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
                 'fieldLabel' => 'Standardeingabe',
                 'name'       => 'value',
                 'width'      => 150),
           array('type'       => 'ComboBox',
                 'fieldLabel' => 'Validierer',
                 'store'      => array('data'=>array(array('',                           'keiner'),
                                                     array('Zend_Validate_EmailAddress', 'E-Mail-Adresse'),
                                                     array('Zend_Validate_Date',         'Datum'))),
                 'hiddenName' => 'validator',
                 'editable'   => false,
                 'triggerAction'=>'all'),
                 );

    protected $_buttons = array('save'   => true);
    protected $_tableName = 'Vpc_Formular_Textbox_IndexModel';
    protected $_primaryKey = 'id';
}
