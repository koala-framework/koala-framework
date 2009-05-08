<?php
class Vpc_Advanced_DownloadsTree_ProjectController extends Vps_Controller_Action_Auto_Form
{
    protected $_permissions = array('save'=>true, 'add'=>true);

    protected function _initFields()
    {
        $this->_form->setModel(Vps_Model_Abstract::getInstance('Vpc_Advanced_DownloadsTree_Projects'));

        $this->_form->add(new Vps_Form_Field_TextField('text', trl('Text')));
        $this->_form->add(new Vps_Form_Field_Checkbox('visible', trl('Sichtbar')));
    }

    protected function _beforeInsert(Vps_Model_Row_Interface $row)
    {
        $row->parent_id = $this->_getParam('parent_id');
        if ($row->parent_id == 0) {
            $row->parent_id = null;
        }
        $row->component_id = $this->_getParam('componentId');
    }

}
