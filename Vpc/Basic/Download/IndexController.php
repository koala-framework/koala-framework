<?php
class Vpc_Basic_Download_IndexController extends Vps_Controller_Action_Auto_Form_Vpc
{
    protected $_buttons = array (
        'save' => true
    );

    public function _initFields()
    {
        $this->_form->setTable(new Vpc_Basic_Download_IndexModel());
        $this->_form->setFileUpload(true);
        $fields = $this->_form->fields;
        $fields->add(new Vps_Auto_Field_TextField('name'))
            ->setFieldLabel('Filename');
        $fields->add(new Vps_Auto_Field_TextArea('info'))
            ->setFieldLabel('Info');
        $fields->add(new Vps_Auto_Field_File('BasicDownload/', $this->component->getSetting('extensions')))
            ->setFieldLabel('File');
    }
    
}