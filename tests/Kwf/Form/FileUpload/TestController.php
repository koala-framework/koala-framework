<?php
/*
 * hier gibt es nur einen test controller da der fileupload nicht per selenium test überprüft werden
 * kann
 * /kwf/test/kwf_form_file-upload_test
 */
class Kwf_Form_FileUpload_TestController extends Kwf_Controller_Action_Auto_Form
{
    protected $_modelName = 'Kwf_Form_Cards_TopModel';
    protected $_permissions = array('save', 'add');
    protected $_buttons = array('save');
    protected $_temp = "";

    public function indexAction()
    {
        parent::indexAction();
        $this->view->assetsPackage = new Kwf_Assets_Package_TestPackage('Kwf_Form_FileUpload');
        $this->view->viewport = 'Kwf.Test.Viewport';
    }

    protected function _initFields()
    {
        $this->_form->add(new Kwf_Form_Field_File("filename", "Filename"))
            ->setMaxResolution(200);

        $this->_form->add(new Kwf_Form_Field_File("filename", "Filename2"))
            ->setMaxResolution(200);

        $this->_form->add(new Kwf_Form_Field_File("filename", "No preview"))
            ->setShowPreview(false);

        $this->_form->add(new Kwf_Form_Field_File("filename", "No delete button"))
            ->setShowDeleteButton(false);

        $this->_form->add(new Kwf_Form_Field_File("filename", "West info position"))
            ->setInfoPosition('west');

        $this->_form->add(new Kwf_Form_Field_File("filename", "Combined"))
            ->setShowDeleteButton(false)
            ->setShowPreview(false)
            ->setInfoPosition('west')
            ->setInfoTpl('<a href="{href:htmlEncode}" target="_blank">{filename:htmlEncode}.{extension:htmlEncode}</a> ({fileSize:fileSize})');

    }
}

