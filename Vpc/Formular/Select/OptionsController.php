<?php
class Vpc_Formular_Select_OptionsController extends Vps_Controller_Action_Auto_Grid
{
    protected $_columns = array(array('dataIndex' => 'component_id',
                                      'header'    => 'component_id',
                                      'hidden'    => false),
				                array('dataIndex' => 'value',
				                      'header'    => 'Wert',
				                      'width'     => 100,
				                      'editor'    => array('type' => 'TextField',
				                  					      'allowBlank' => false)),
				                array('dataIndex' => 'text',
				                      'header'    => 'Wert',
				                      'width'     => 100,
				                      'editor'    => array('type' => 'TextField',
				                  					      'allowBlank' => false)),
				                array('dataIndex' => 'selected',
				                      'header'    => 'Angehakt',
				                      'width'     => 50,
				                      'editor'    => 'Checkbox'));

   // protected $_buttons = array();
    //protected $_paging = 20;
    protected $_defaultOrder = 'component_id';
    protected $_tableName = 'Vpc_Formular_Select_OptionsModel';
    //protected $_primaryKey = array ('component_key', 'page_key');
    protected $_primaryKey = 'id';

    protected function _getWhere()
    {
    	$where = parent::_getWhere();
    	$where['component_id = ?'] = $this->_getParam('id');
    	return $where;
    }
}