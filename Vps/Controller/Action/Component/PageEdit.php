<?php

class Vps_Controller_Action_Component_PageEdit extends Vps_Controller_Action_Auto_Form
{
    protected $_permissions = array('save' => true, 'add' => true);

    protected function _initFields()
    {
        $components = Vpc_Admin::getAvailableComponents('Vpc/');
        $types = array(
            'Vpc_Paragraphs_Component' => 'Content',
            'Vpc_Basic_LinkTag_Component' => 'Link'
        );

        $table = new Vps_Dao_Pages();
        $table->showInvisible(true);

        $this->_form->setTable($table);
        $fields = $this->_form->fields;
        $fields->add(new Vps_Auto_Field_TextField('name'))
            ->setFieldLabel('Name of Page')
            ->setAllowBlank(false);
        $fields->add(new Vps_Auto_Field_Select('component_class'))
            ->setFieldLabel('Pagetype')
            ->setValues($types)
            ->setValue('Vpc_Paragraphs_Component')
            ->setAllowBlank(false);
    }

    protected function _beforeInsert(Zend_Db_Table_Row_Abstract $row)
    {
        $row->parent_id = $this->_getParam('parent_id');
    }
}
