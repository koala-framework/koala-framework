<?php
class Vpc_Basic_Download_IndexController extends Vps_Controller_Action_Auto_Vpc_Form
{
    protected $_buttons = array (
        'save' => true
    );

    public function _initFields()
    {
        $this->_form->setTable(new Vpc_Basic_Download_IndexModel());
        $this->_form->add(new Vps_Auto_Field_TextField('filename', 'Filename'))
            ->setAllowBlank(false);
        $this->_form->add(new Vps_Auto_Field_TextArea('infotext', 'Infotext'))
            ->setWidth(300)
            ->setGrow(true);
        $this->_form->add(new Vps_Auto_Field_File('vps_upload_id', 'File'))
            ->setDirectory('BasicDownload/')
            ->setExtensions($this->component->getSetting('extensions'));
    }

}