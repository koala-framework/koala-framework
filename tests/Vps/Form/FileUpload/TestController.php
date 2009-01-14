<?php
/*
 * hier gibt es nur einen test controller da der fileupload nicht per selenium test überprüft werden
 * kann
 */
class Vps_Form_FileUpload_TestController extends Vps_Controller_Action_Auto_Form
{
    protected $_modelName = 'Vps_Form_Cards_TopModel';
    protected $_permissions = array('save', 'add');
    protected $_buttons = array('save');
    protected $_temp = "";

    protected function _initFields()
    {
        $this->_form->add(new Vps_Form_Field_File("filename", "Filename"))
            ->setMaxResolution(200);

    }
}

