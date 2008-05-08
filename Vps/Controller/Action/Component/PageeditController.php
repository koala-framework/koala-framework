<?php

class Vps_Controller_Action_Component_PageEditController extends Vps_Controller_Action_Auto_Form
{
    protected $_permissions = array('save' => true, 'add' => true);

    protected function _initFields()
    {
        $types = array();
        foreach (Zend_Registry::get('config')->vpc->pageClasses as $c) {
            if ($c->class && $c->text) {
                $types[$c->class] = $c->text;
            }
        }

        $table = new Vps_Dao_Pages();
        $table->showInvisible(true);

        $this->_form->setTable($table);
        $fields = $this->_form->fields;
        $fields->add(new Vps_Form_Field_TextField('name', 'Name of Page'))
            ->setAllowBlank(false);
        $fields->add(new Vps_Form_Field_Select('component_class', 'Pagetype'))
            ->setValues($types)
            ->setValue(0)
            ->setAllowBlank(false);
        $fields->add(new Vps_Form_Field_Checkbox('hide', 'Hide in Menu'));

        $cfg = Zend_Registry::get('config');
        $decorators = $cfg->vpc->pageDecorators ? $cfg->vpc->pageDecorators : array();
        foreach ($decorators as $decorator) {
            $formClass = Vpc_Admin::getComponentFile($decorator, 'Form', 'php', true);
            if ($formClass) {
                $form = new $formClass($decorator, $this->_getParam('id'));
                $form->setBaseCls('x-plain');
                $title = Vpc_Abstract::getSetting($decorator, 'componentName');
                if ($title) {
                    $fieldset = new Vps_Form_Container_FieldSet($title);
                    $fieldset->add($form);
                    $fields->add($fieldset);
                } else {
                    $fields->add($form);
                }
            }
        }
    }

    protected function _beforeInsert(Vps_Model_Row_Interface $row)
    {
        $row->parent_id = $this->_getParam('parent_id');
    }
}
