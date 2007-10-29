<?php
class Vpc_Basic_Rte_IndexController extends Vps_Controller_Action_Auto_Vpc_Form
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

    public function jsonAddImageAction()
    {
        $html = $this->_getParam('html');
        $this->component->saveSetting('text_edit', $html);
        $image = $this->component->addImage();
        $this->view->config = $this->view->getConfig($image);
    }
}
