<?php

class Vps_Controller_Action_Component_PageEditController extends Vps_Controller_Action_Auto_Form
{
    protected $_permissions = array('save' => true, 'add' => true);
    protected $_tableName = 'Vps_Dao_Pages';

    protected function _initFields()
    {
        $types = array();
        $classes = Vpc_Abstract::getChildComponentClasses(Vps_Registry::get('config')->vpc->rootComponent, 'page');
        foreach ($classes as $component=>$class) {
            $name = Vpc_Abstract::getSetting($class, 'componentName');
            if ($name) {
                $name = str_replace('.', ' ', $name);
                $types[$component] = $name;
            }
        }

        $fields = $this->_form->fields;
        $fields->add(new Vps_Form_Field_TextField('name', 'Name of Page'))
            ->setAllowBlank(false);
        $fields->add(new Vps_Form_Field_Select('component', 'Pagetype'))
            ->setValues($types)
            ->setAllowBlank(false);
        $fields->add(new Vps_Form_Field_Checkbox('hide', 'Hide in Menu'));
    }

    protected function _beforeInsert(Vps_Model_Row_Interface $row)
    {
        $row->parent_id = $this->_getParam('parent_id');
    }
}
