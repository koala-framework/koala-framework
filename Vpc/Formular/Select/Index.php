<?php
class Vpc_Formular_Select_Index extends Vpc_Formular_Field_Decide_Abstract
{
    protected $_defaultSettings = array('rows' => '10', 'name' => '');
    protected $_options = array();

    public function getTemplateVars($mode)
    {
        $rows = $this->getSetting('rows');
        $name = $this->getSetting('name');

        $return['rows'] = $rows;
        $return['name'] = $name;
        if ($this->_options == null) $this->getOptions();

        $return['options'] = $this->_options;
        $return['id'] = $this->getComponentId();

        $return['template'] = 'Formular/Select.html';
        return $return;
    }

    public function getOptions ()
    {
        $table = $this->_getTable('Vpc_Formular_Select_OptionsModel');
        $select = $table->fetchAll(array(    'component_id = ?'  => $this->getComponentId(),
                                             'page_key = ?'      => $this->getPageKey(),
                                             'component_key = ?' => $this->getComponentKey()));
        //values werden rausgeschrieben
        $values = array();
        foreach ($select as $option) {
            $this->_options[] = array('value' => $option->value, 'text' => $option->value, 'selected' => $option->selected, 'id' => $option->id);
        }

    }
}