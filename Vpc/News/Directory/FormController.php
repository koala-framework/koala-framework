<?php
class Vpc_News_Directory_FormController extends Vps_Controller_Action_Auto_Form
{
    protected $_buttons = array();
    protected $_permissions = array('save', 'add');

    public function _initFields()
    {
        $data = Vps_Component_Data_Root::getInstance()
                        ->getComponentByDbId($this->_getParam('component_id'));

        $this->_form = Vpc_Abstract_Form::createChildComponentForm(
                $data->componentClass, '-detail', $data->componentClass);
        $this->_form->setIdTemplate(null);
        $tablename = Vpc_Abstract::getSetting($data->componentClass, 'tablename');
        $this->_form->setTable(new $tablename(array('componentClass'=>$data->componentClass)));

        $classes = Vpc_Abstract::getChildComponentClasses($data->componentClass);
        foreach ($classes as $class) {
            $formName = Vpc_Admin::getComponentClass($class, 'NewsEditForm');
            if ($formName) {
                $this->_form->add(new $formName($class, $class))
                    ->setIdTemplate('{0}');
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
