<?php
class Vpc_Formular_Multicheckbox_CheckboxesController extends Vpc_Formular_Field_FormGrid
{
    protected $_columns = array(array('dataIndex' => 'value',
				                      'header'    => 'Wert',
				                      'width'     => 100,
				                      'editor'    => array('type' => 'TextField',
				                  					      'allowBlank' => false)),
				                array('dataIndex' => 'text',
				                      'header'    => 'Bezeichnung',
				                      'width'     => 200,
				                      'editor'    => array('type' => 'TextField',
				                  					      'allowBlank' => true)),
				                array('dataIndex' => 'checked',
				                      'header'    => 'Angehakt',
				                      'width'     => 50,
				                      'editor'    => 'Checkbox'));

   // protected $_buttons = array();
    protected $_paging = 20;
    protected $_defaultOrder = 'page_id';
    protected $_tableName = 'Vpc_Formular_Checkbox_IndexModel';
    //protected $_primaryKey = array ('component_key', 'page_key');
    protected $_primaryKey = 'component_key';

    protected function _getWhere()
    {
    	$where = parent::_getWhere();
    	$where['page_id = ?'] = $this->component->getDbId();
    	return $where;
    }


}