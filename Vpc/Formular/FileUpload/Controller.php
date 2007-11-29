<?php
class Vpc_Formular_FileUpload_Controller extends Vps_Controller_Action_Auto_Vpc_Form
{
    protected $_fields = array(
            array('type'       => 'TextField',
                  'fieldLabel' => 'Breite',
                  'name'       => 'width',
                  'width'      => 50),
            array('type'       => 'TextField',
                  'fieldLabel' => 'Max. Dateigröße (in kb)',
                  'name'       => 'maxSize',
                  'width'      => 50),
            array('type'       => 'TextField',
                  'fieldLabel' => 'Erlaubte Typen (zB. jpg, gif)',
                  'name'       => 'types_allowed',
                  'width'      => 150),
    );

    protected $_buttons = array('save'   => true);
    protected $_tableName = 'Vpc_Formular_FileUpload_Model';

}
