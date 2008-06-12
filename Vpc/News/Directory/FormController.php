<?php
class Vpc_News_Directory_FormController extends Vps_Controller_Action_Auto_Form
{
    protected $_buttons = array();
    protected $_permissions = array('save', 'add');

    public function _initFields()
    {
        $tc = new Vps_Dao_TreeCache();
        $component = $tc->findByDbId($this->_getParam('component_id'))->current();
        $classes = Vpc_Abstract::getSetting($component->component_class, 'childComponentClasses');

        $this->_form = Vpc_Abstract_Form::createComponentForm($classes['detail'], $classes['detail']);
        $tablename = Vpc_Abstract::getSetting($this->class, 'tablename');
        $this->_form->setTable(new $tablename(array('componentClass'=>$this->class)));

        foreach ($classes as $class) {
            $formName = Vpc_Admin::getComponentClass($class, 'NewsEditForm');
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
