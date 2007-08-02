<?php
class Vpc_Formular_SelectMultiple_Index extends Vpc_Formular_Field_Decide_Abstract
{
    protected $_defaultSettings = array('rows' => '10', 'name' => '');
    protected $_options = array();

    public function getTemplateVars()
    {
		$rows = $this->getSetting('rows');
		$name = $this->getSetting('name');

		$return['rows'] = $rows;
		$return['name'] = $name;
		if ($this->_options == null) $this->getOptions();

		$return['options'] = $this->_options;
		$return['id'] = $this->getDbId().$this->getComponentKey();

		$return['template'] = 'Formular/SelectMultiple.html';
		return $return;
    }

    public function getOptions ()
    {
        $table = $this->_getTable('Vpc_Formular_SelectMultiple_OptionsModel');
        $select = $table->fetchAll(array(    'page_id = ?'      => $this->getDbId(),
                                             'component_key = ?' => $this->getComponentKey()));
        //values werden rausgeschrieben
        $values = array();
        foreach ($select as $option) {
            $this->_options[] = array('value' => $option->value, 'text' => $option->value, 'selected' => $option->selected, 'id' => $option->id);
        }

    }

    public function processInput()
    {
	    if (isset($_POST[$this->getSetting('name')])){
	        $this->getOptions();

	        $selectedValues = $_POST[$this->getName()];

	        foreach ($this->_options AS $key => $option) {
	            $option['selected'] = '0';
	        }
	        foreach ($this->_options AS $key => $option) {
			    if (in_array($option['value'], $selectedValues)){
			       $option['selected'] = '1';
			    }
			    $this->_options[$key] = $option;
		    }
	    }
    }
}