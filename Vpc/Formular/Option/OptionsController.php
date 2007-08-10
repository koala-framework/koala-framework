<?php
class Vpc_Formular_Option_OptionsController extends Vpc_Formular_Field_FormGrid
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
				                      'editor'    => 'Checkbox',
				                      ));


    protected $_defaultOrder = 'page_id';
    protected $_tableName = 'Vpc_Formular_Option_OptionsModel';
    protected $_primaryKey = 'id';

    protected function _getWhere()
    {
    	$where = parent::_getWhere();
    	$where['page_id = ?'] = $this->component->getDbId();
    	$where['component_key = ?'] = $this->component->getComponentkey();
    	return $where;
    }
}
