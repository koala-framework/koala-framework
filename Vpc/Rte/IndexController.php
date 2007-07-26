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

	public function init()
	{
		$components = new Vps_Config_Ini('application/components.ini');
		$component = $components->Vpc_Rte_Index;
		$fields = $this->_fields[0];
		foreach ($component AS $key => $data){
		   if ($key == 'fieldLabel')$fields[$key] = $data;
		   else $fields[$key] = (int)$data;
		}
		$this->_fields[0] = $fields;
		parent::init();
	}


}
