<?php

class Vps_Controller_Action_Component_PageEdit extends Vps_Controller_Action_Auto_Form
{
    protected $_permissions = array('save' => true, 'add' => true);

    protected function _initFields()
    {
        $types = array();
        foreach (Zend_Registry::get('config')->pageClasses as $c) {
            if ($c->class && $c->text) {
                $types[$c->class] = $c->text;
            }
        }

        $table = new Vps_Dao_Pages();
        $table->showInvisible(true);

        $this->_form->setTable($table);
        $fields = $this->_form->fields;
        $fields->add(new Vps_Auto_Field_TextField('name', 'Name of Page'))
            ->setAllowBlank(false);
        $fields->add(new Vps_Auto_Field_Select('component_class', 'Pagetype'))
            ->setValues($types)
            ->setValue('Vpc_Paragraphs_Component')
            ->setAllowBlank(false);
        $fields->add(new Vps_Auto_Field_Checkbox('hide', 'Hide in Menu'));
    }

    protected function _beforeInsert(Zend_Db_Table_Row_Abstract $row)
    {
        $row->parent_id = $this->_getParam('parent_id');
    }
}
