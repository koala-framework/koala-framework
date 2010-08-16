<?php
class Vpc_Advanced_DownloadsTree_DownloadController extends Vps_Controller_Action_Auto_Form
{
    protected $_permissions = array('save'=>true, 'add'=>true);

    protected function _initFields()
    {
        $modelName = Vpc_Abstract::getSetting($this->_getParam('class'), 'downloadsModel');
        $this->_form->setModel(Vps_Model_Abstract::getInstance($modelName));

        $this->_form->add(new Vps_Form_Field_TextField('text', trlVps('Document')));
        $this->_form->add(new Vps_Form_Field_DateField('date', trlVps('Date')))
            ->setDefaultValue(date('Y-m-d'));
        $this->_form->add(new Vps_Form_Field_Checkbox('visible', trlVps('Visible')));
        $this->_form->add(new Vps_Form_Field_File('File', trlVps('File')))
            ->setAllowBlank(false);
    }

    protected function _beforeInsert(Vps_Model_Row_Interface $row)
    {
        $row->project_id = $this->_getParam('project_id');
    }
}
