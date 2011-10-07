<?php
class Kwc_Advanced_DownloadsTree_ProjectController extends Kwf_Controller_Action_Auto_Form
{
    protected $_permissions = array('save'=>true, 'add'=>true);

    protected function _initFields()
    {
        $modelName = Kwc_Abstract::getSetting($this->_getParam('class'), 'projectsModel');
        $this->_form->setModel(Kwf_Model_Abstract::getInstance($modelName));

        $this->_form->add(new Kwf_Form_Field_TextField('text', trlKwf('Text')));
        $this->_form->add(new Kwf_Form_Field_Checkbox('visible', trlKwf('Visible')));
    }

    protected function _beforeInsert(Kwf_Model_Row_Interface $row)
    {
        $row->parent_id = $this->_getParam('parent_id');
        if ($row->parent_id == 0) {
            $row->parent_id = null;
        }
        $row->component_id = $this->_getParam('componentId');
    }

}
