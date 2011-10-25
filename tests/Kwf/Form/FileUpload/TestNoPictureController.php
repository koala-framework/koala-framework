<?php
/*
 * hier gibt es nur einen test controller da der fileupload nicht per selenium test überprüft werden
 * kann
 */
class Kwf_Form_FileUpload_TestNoPictureController extends Kwf_Controller_Action_Auto_Form
{
    protected $_modelName = 'Kwf_Form_Cards_TopModel';
    protected $_permissions = array('save', 'add');
    protected $_buttons = array('save');
    protected $_temp = "";

    protected function _initFields()
    {
        $this->_form->add(new Kwf_Form_Field_File("filename", "Filename"));

    }
}

