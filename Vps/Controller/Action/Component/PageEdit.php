<?php

class Vps_Controller_Action_Component_PageEdit extends Vps_Controller_Action_Auto_Form
{
    protected $_permissions = array('save' => true, 'add' => true);

    protected function _initFields()
    {
        $components = Vpc_Admin::getAvailableComponents('Vpc/');
        $types = array('Vpc_Paragraphs_Index' => 'Content');
        foreach ($components as $name => $component) {
            //$type = constant("$component::TYPE");
            //if ($type != '') {
                //$types[$component] = $type;
            //}
        }

        $table = new Vps_Dao_Pages();
        $table->showInvisible(true);

        $this->_form->setTable($table);
        $fields = $this->_form->fields;
        $fields->add(new Vps_Auto_Field_TextField('name'))
            ->setFieldLabel('Name of Page');
        $fields->add(new Vps_Auto_Field_TextField('title'))
            ->setFieldLabel('Title of Page');
        $fields->add(new Vps_Auto_Field_TextField('pagetitle'))
            ->setFieldLabel('Headline');
        $fields->add(new Vps_Auto_Field_ComboBox('component_class'))
            ->setFieldLabel('Pagetype')
            ->setValues($types)
            ->setTriggerAction('all')
            ->setValue('Vpc_Paragraphs_Index')
            ->setEditable(false)
            ->setForceSelection(true);
    }

    protected function _beforeInsert(Zend_Db_Table_Row_Abstract $row)
    {
        $row->parent_id = $this->_getParam('parent_id');
    }
}
