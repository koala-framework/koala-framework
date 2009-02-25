<?php
class Vpc_Directories_Item_Directory_FormController extends Vps_Controller_Action_Auto_Form
{
    protected $_buttons = array();
    protected $_permissions = array('save', 'add');

    public function _initFields()
    {
        $data = Vps_Component_Data_Root::getInstance()
                        ->getComponentByDbId($this->_getParam('componentId'), array('ignoreVisible' => true));

        $this->_form = Vpc_Abstract_Form::createChildComponentForm(
                $data->componentClass, '-detail', $data->componentClass);
        $this->_form->setIdTemplate(null);

        $this->_form->setModel(Vpc_Abstract::createModel($data->componentClass));

        $classes = Vpc_Abstract::getChildComponentClasses($data->componentClass);
        foreach ($classes as $class) {
            $formName = Vpc_Admin::getComponentClass($class, 'ItemEditForm');
            if ($formName) {
                $this->_form->add(new $formName($class, $class));
            }
        }
    }

    public function _beforeSave($row)
    {
        if ($this->_getParam('id') == 0 && $this->_getParam('componentId')) {
            if (isset($row->component_id)) $row->component_id = $this->_getParam('componentId');
            if (isset($row->visible)) $row->visible = 0;
        }
    }
}
