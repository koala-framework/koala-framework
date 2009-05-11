<?php
class Vpc_Advanced_DownloadsTree_DownloadController extends Vps_Controller_Action_Auto_Form
{
    protected $_permissions = array('save'=>true, 'add'=>true);

    protected function _initFields()
    {
        $this->_form->setModel(Vps_Model_Abstract::getInstance('Vpc_Advanced_DownloadsTree_Downloads'));

        $this->_form->add(new Vps_Form_Field_TextField('text', trl('Dokument')));
        $this->_form->add(new Vps_Form_Field_DateField('date', trl('Datum')))
            ->setDefaultValue(date('Y-m-d'));
        $this->_form->add(new Vps_Form_Field_Checkbox('visible', trl('Sichtbar')));
        $this->_form->add(new Vps_Form_Field_File('File', trl('Datei')))
            ->setAllowBlank(false);
    }

    protected function _beforeInsert(Vps_Model_Row_Interface $row)
    {
        $row->project_id = $this->_getParam('project_id');
    }
}
