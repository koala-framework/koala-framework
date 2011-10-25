<?php
class Kwc_Advanced_DownloadsTree_DownloadController extends Kwf_Controller_Action_Auto_Form
{
    protected $_permissions = array('save'=>true, 'add'=>true);

    protected function _initFields()
    {
        $modelName = Kwc_Abstract::getSetting($this->_getParam('class'), 'downloadsModel');
        $this->_form->setModel(Kwf_Model_Abstract::getInstance($modelName));

        $this->_form->add(new Kwf_Form_Field_TextField('text', trlKwf('Document')));
        $this->_form->add(new Kwf_Form_Field_DateField('date', trlKwf('Date')))
            ->setDefaultValue(date('Y-m-d'));
        $this->_form->add(new Kwf_Form_Field_Checkbox('visible', trlKwf('Visible')));
        $this->_form->add(new Kwf_Form_Field_File('File', trlKwf('File')))
            ->setAllowBlank(false);
    }

    protected function _beforeInsert(Kwf_Model_Row_Interface $row)
    {
        $row->project_id = $this->_getParam('project_id');
    }
}
