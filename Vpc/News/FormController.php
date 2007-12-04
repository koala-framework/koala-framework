<?php
class Vpc_News_FormController extends Vps_Controller_Action_Auto_Form
{
    protected $_buttons = array();
    protected $_permissions = array('save' => true, 'add' => true);
    
    public function preDispatch()
    {
        $tablename = Vpc_Abstract::getSetting($this->class, 'tablename');
        $this->_table = new $tablename(array('componentClass'=>$this->class));
        parent::preDispatch();
    }
    
    public function _initFields()
    {
        $this->_form->add(new Vps_Auto_Field_TextField('title', 'Title'))
            ->setAllowBlank(false);
        $this->_form->add(new Vps_Auto_Field_TextArea('teaser', 'Teaser'))
            ->setWidth(300)
            ->setHeight(100);
        $this->_form->add(new Vps_Auto_Field_DateField('publish_date', 'Publish Date'))
            ->setAllowBlank(false);
        $this->_form->add(new Vps_Auto_Field_DateField('expiry_date', 'Expiry Date'));
    }

    public function _beforeSave($row)
    {
        if ($this->_getParam('id') == 0 && $this->_getParam('pageId')) {
            $row->page_id = $this->_getParam('pageId');
            $row->component_key = $this->_getParam('componentKey');
            $row->pos = 0;
            $row->visible = 0;
        }
    }
}
