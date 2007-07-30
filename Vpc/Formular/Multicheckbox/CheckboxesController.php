<?php
class Vpc_Formular_Multicheckbox_CheckboxesController extends Vps_Controller_Action_Auto_Grid
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
    protected $_defaultOrder = 'component_id';
    protected $_tableName = 'Vpc_Formular_Checkbox_IndexModel';
    //protected $_primaryKey = array ('component_key', 'page_key');
    protected $_primaryKey = 'component_key';

    protected function _getWhere()
    {
    	$where = parent::_getWhere();
    	$where['component_id = ?'] = $this->_getParam('id');
    	return $where;
    }

    public function jsonSaveAction()
    {
        if(!isset($this->_permissions['save']) || !$this->_permissions['save']) {
            throw new Vps_Exception("Save is not allowed.");
        }
        $success = false;

        $data = Zend_Json::decode($this->getRequest()->getParam("data"));
        $addedIds = array();
        foreach ($data as $submitRow) {
            $id = $submitRow[$this->_primaryKey];
            if ($id) {
            	//hier eine kleine änderung
                //$row = $this->_table->find($id)->current();
                $row = $this->_table->fetchall(array('component_id = ?' => $this->_getParam('id'),
													 'component_key = ?' => $id))->current();
            } else {
                if(!isset($this->_permissions['add']) || !$this->_permissions['add']) {
                    throw new Vps_Exception("Add is not allowed.");
                }
                $row = $this->_table->createRow();
            }
            if(!$row) {
                throw new Vps_Exception("Can't find row with id '$id'.");
            }
            foreach ($this->_columns as $col) {
                if ((isset($col['allowSave']) && $col['allowSave'])
                    || (isset($col['editor']) && $col['editor']))
                {
                    if (isset($submitRow[$col['dataIndex']])) {
                        $row->$col['dataIndex'] = $submitRow[$col['dataIndex']];
                    }
                    if ($col['type'] == 'boolean') {
                        if(isset($submitRow[$col['dataIndex']])) {
                            $val = 1;
                        } else {
                            $val = 0;
                        }
                        $row->$col['dataIndex'] = $val;
                    }
                }
            }
            if (!$id) {
                $this->_beforeInsert($row);
            }
            $this->_beforeSave($row);
            $row->save();
            if (!$id) {
                $this->_afterInsert($row);
            }
            $this->_afterSave($row);
            if (!$id) {
                $addedIds[] = $row->id;
            }
        }
        $success = true;

        if ($addedIds) {
            $this->view->addedIds = $addedIds;
        }
        $this->view->success = $success;
    }

}