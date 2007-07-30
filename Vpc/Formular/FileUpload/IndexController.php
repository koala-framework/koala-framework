<?php
class Vpc_Formular_FileUpload_IndexController extends Vps_Controller_Action_Auto_Form_Vpc
{
    protected $_fields = array(
            array('type'       => 'TextField',
                  'fieldLabel' => 'Breite',
                  'name'       => 'width',
                  'width'      => 50),
            array('type'       => 'TextField',
                  'fieldLabel' => 'Max. Anzahl an Zeichen',
                  'name'       => 'maxSize',
                  'width'      => 50),
            array('type'       => 'TextField',
                  'fieldLabel' => 'Erlaubte Typen (zB. jpg, gif)',
                  'name'       => 'types_allowed',
                  'width'      => 150),
    );

    protected $_buttons = array('save'   => true);
    protected $_tableName = 'Vpc_Formular_FileUpload_IndexModel';
    protected $_primaryKey = 'id';

}
