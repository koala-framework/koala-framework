<?php
class Vpc_Basic_Text_Controller extends Vps_Controller_Action_Auto_Vpc_Form
{
    protected $_buttons = array('save'   => true);

    public function _initFields()
    {
        $field = new Vps_Auto_Field_HtmlEditor('content', 'Content');
        foreach ($this->component->getSettings() as $key => $val) {
            if ($key != 'content') {
                $method = 'set' . ucfirst($key);
                $field->$method($val);
            }
        }
        $controllerUrl = Vpc_Admin::getInstance($this->component)
                            ->getControllerUrl($this->component);
        $field->setControllerUrl($controllerUrl);
        $this->_form->add($field);
    }

    protected function _beforeSave(Zend_Db_Table_Row_Abstract $row)
    {
        parent::_beforeSave($row);
        $this->component->beforeSave($row->content);
        $row->content_edit = '';
    }

    public function jsonAddImageAction()
    {
        $image = $this->component->addImage($this->_getParam('content'));
        $this->view->config = Vpc_Admin::getInstance($image)->getConfig($image);
    }

    public function jsonEditImageAction()
    {
        $image = $this->component->getImageBySrc($this->_getParam('src'));
        if (!$image) {
            throw new Vps_Exception("Can't find image component");
        }
        $this->view->config = Vpc_Admin::getInstance($image)->getConfig($image);
    }

    public function jsonAddLinkAction()
    {
        $link = $this->component->addLink($this->_getParam('content'));
        $this->view->config = Vpc_Admin::getInstance($link)->getConfig($link);
    }

    public function jsonEditLinkAction()
    {
        $link = $this->component->getLinkByHref($this->_getParam('href'));
        if (!$link) {
            throw new Vps_Exception("Can't find link component");
        }
        $this->view->config = Vpc_Admin::getInstance($link)->getConfig($link);
    }
}
