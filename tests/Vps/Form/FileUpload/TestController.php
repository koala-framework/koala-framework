<?php
/*
 * hier gibt es nur einen test controller da der fileupload nicht per selenium test überprüft werden
 * kann
 * /vps/test/vps_form_file-upload_test
 */
class Vps_Form_FileUpload_TestController extends Vps_Controller_Action_Auto_Form
{
    protected $_modelName = 'Vps_Form_Cards_TopModel';
    protected $_permissions = array('save', 'add');
    protected $_buttons = array('save');
    protected $_temp = "";

    public function indexAction()
    {
        parent::indexAction();
        $this->view->assetsType = 'Vps_Form_FileUpload:Test';
        $this->view->viewport = 'Vps.Test.Viewport';
    }

    protected function _initFields()
    {
        $this->_form->add(new Vps_Form_Field_File("filename", "Filename"))
            ->setMaxResolution(200);

        $this->_form->add(new Vps_Form_Field_File("filename", "Filename2"))
            ->setMaxResolution(200);

        $this->_form->add(new Vps_Form_Field_File("filename", "No preview"))
            ->setShowPreview(false);

        $this->_form->add(new Vps_Form_Field_File("filename", "No delete button"))
            ->setShowDeleteButton(false);

        $this->_form->add(new Vps_Form_Field_File("filename", "West info position"))
            ->setInfoPosition('west');

        $this->_form->add(new Vps_Form_Field_File("filename", "Combined"))
            ->setShowDeleteButton(false)
            ->setShowPreview(false)
            ->setInfoPosition('west')
            ->setInfoTpl('<a href="{href}" target="_blank">{filename}.{extension}</a> ({fileSize:fileSize})');

    }
}

