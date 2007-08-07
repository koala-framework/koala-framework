<?php
class Vpc_Rte_IndexController extends  Vps_Controller_Action_Auto_Form_Vpc
{
	protected $_fields = array(
            array('type'               => 'HtmlEditor',
                  'fieldLabel'         => 'Html Editor:',
                  'name'               => 'text',
                  'width'              => 550,
                  'height'             => 225,
                  'enableAlignments'   => 0,
                  'enableColors'       => 0,
                  'enableFont'         => 0,
                  'enableFontSize'     => 1,
                  'enableFormat'       => 0,
                  'enableLinks'        => 0,
                  'enableLists'        => 0,
                  'enableSourceEdit'   => 0),
            );

    protected $_buttons = array('save'   => true);
    protected $_tableName = 'Vpc_Rte_IndexModel';
    protected $_primaryKey = 'id';

	public function preDispatch()
	{
		$fields = $this->_fields[0];
		$newSettings = $this->component->getSettings();
		foreach ($fields as  $fieldKey => $fieldData){
			if (array_key_exists($fieldKey, $newSettings)){
				$fields[$fieldKey] = $newSettings[$fieldKey];
			}
		}
		$this->_fields[0] = $fields;
	}


}
