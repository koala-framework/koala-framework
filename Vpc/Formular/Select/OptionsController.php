<?php
class Vpc_Formular_Select_OptionsController extends Vpc_Formular_Field_FormGrid
{
    protected $_columns = array(array('dataIndex' => 'value',
				                      'header'    => 'Wert',
				                      'width'     => 100,
				                      'editor'    => array('type' => 'TextField',
				                  					      'allowBlank' => false)),
				                array('dataIndex' => 'text',
				                      'header'    => 'Ausgabe',
				                      'width'     => 100,
				                      'editor'    => array('type' => 'TextField',
				                  					      'allowBlank' => false)),
				                array('dataIndex' => 'selected',
				                      'header'    => 'Angehakt',
				                      'width'     => 50,
				                      'editor'    => 'Checkbox'));

   // protected $_buttons = array();
    //protected $_paging = 20;
    protected $_defaultOrder = 'page_id';
    protected $_tableName = 'Vpc_Formular_Select_OptionsModel';
    //protected $_primaryKey = array ('component_key', 'page_key');
    protected $_primaryKey = 'id';

    protected function _getWhere()
    {
    	$where = parent::_getWhere();
    	$where['page_id = ?'] = $this->component->getDbId();
    	return $where;
    }
}