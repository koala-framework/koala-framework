<?php
class Vpc_Basic_Text_IndexController extends Vps_Controller_Action_Auto_Vpc_Form
{
    protected $_buttons = array('save'   => true);

    public function _initFields()
    {
        $field = new Vps_Auto_Field_HtmlEditor('text', 'Content');
        foreach ($this->component->getSettings() as $key => $val) {
            if ($key != 'text') {
                $method = 'set' . ucfirst($key);
                $field->$method($val);
            }
        }
        $field->setControllerUrl($this->view->getControllerUrl($this->component));
        $this->_form->add($field);
    }

    protected function _beforeSave(Zend_Db_Table_Row_Abstract $row)
    {
        parent::_beforeSave($row);
        $this->component->beforeSave($row->text);
        $row->text_edit = '';
    }

    public function jsonAddImageAction()
    {
        $image = $this->component->addImage($this->_getParam('html'));
        $this->view->config = $this->view->getConfig($image);
    }

    public function jsonEditImageAction()
    {
        $image = $this->component->getImageBySrc($this->_getParam('src'));
        if (!$image) {
            throw new Vps_Exception("Can't find image component");
        }
        $this->view->config = $this->view->getConfig($image);
    }
}
