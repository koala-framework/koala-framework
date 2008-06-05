<?php
class Vpc_News_Directory_FormController extends Vps_Controller_Action_Auto_Form
{
    protected $_buttons = array();
    protected $_permissions = array('save', 'add');

    public function preDispatch()
    {
        p($this->class);
        $tablename = Vpc_Abstract::getSetting($this->class, 'tablename');
        $this->_table = new $tablename(array('componentClass'=>$this->class));
        parent::preDispatch();
    }

    public function _initFields()
    {
        $this->_form->add(new Vps_Form_Field_TextField('title', trlVps('Title')))
            ->setAllowBlank(false)
            ->setWidth(300);
        $this->_form->add(new Vps_Form_Field_TextArea('teaser', trlVps('Teaser')))
            ->setWidth(300)
            ->setHeight(100);
        $this->_form->add(new Vps_Form_Field_DateField('publish_date', trlVps('Publish Date')))
            ->setAllowBlank(false);
        $this->_form->add(new Vps_Form_Field_DateField('expiry_date', trlVps('Expiry Date')));


@work: $this->class ist FALSCH
        $classes = Vpc_Abstract::getSetting($this->class, 'childComponentClasses');
        p($this->class);
        foreach ($classes as $class) {
            $formName = Vpc_Admin::getComponentClass($class, 'NewsEditForm');
            p($class);
            p($formName);
            if ($formName) {
                $this->_form->add(new $formName($class, $class));
            }
        }
    }

    public function _beforeSave($row)
    {
        if ($this->_getParam('id') == 0 && $this->_getParam('component_id')) {
            $row->component_id = $this->_getParam('component_id');
            $row->visible = 0;
        }
    }
}
