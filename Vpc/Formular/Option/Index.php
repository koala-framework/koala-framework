<?php
class Vpc_Formular_Option_Index extends Vpc_Formular_Field_Decide_Abstract
{
	protected $_settings = array (
								'text' => '',
								'value' => '',
								'name' => '',
								'checked' => 0,
								'horizontal' => 0);

	protected $_tablename = 'Vpc_Formular_Option_IndexModel';
    public $controllerClass = 'Vpc_Formular_Option_IndexController';
    const NAME = 'Formular.Option';

	protected $_options = array ();

	public function getTemplateVars()
	{
		if ($this->_options == null)
			$this->getOptions();
		$return['options'] = $this->_options;
		$return['horizontal'] = $this->getSetting('horizontal');
		$return['name'] = $this->getSetting('name');
		$return['id'] = $this->getDbId().$this->getComponentKey();
		$return['template'] = 'Formular/Option.html';
		return $return;
	}

	public function getOptions()
	{
		$table = $this->_getTable('Vpc_Formular_Option_OptionsModel');
		$select = $table->fetchAll(array ('page_id = ?' => $this->getDbId(), 'component_key = ?' => $this->getComponentKey()));
		//values werden rausgeschrieben

		foreach ($select as $option)
		{
			$this->_options[] = array (
				'value' => $option->value,
				'text' => $option->text,
				'checked' => $option->checked,
				'id' => $option->id
			);
		}
	}
}