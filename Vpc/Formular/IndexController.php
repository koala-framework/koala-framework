<?php
class Vpc_Formular_IndexController extends Vpc_Paragraphs_IndexController
{
	 protected $_columns = array(
            array('dataIndex' => 'component_class',
                  'header'    => 'Komponente',
                  'width'     => 300),
            array('dataIndex' => 'visible',
                  'header'    => 'Sichtbar',
                  'width'     => 50,
                  'editor'    => 'Checkbox',
                  ),
			array('dataIndex' => 'name',
                  'header'    => 'Bezeichnung',
                  'width'     => 150,
                  'editor'    => 'TextField',
                  ),
            array('dataIndex' => 'mandatory',
                  'header'    => 'Verpflichtend',
                  'width'     => 80,
                  'editor'    => 'Checkbox',
                  ),
            array('dataIndex' => 'no_cols',
                  'header'    => 'noCols',
                  'width'     => 50,
                  'editor'    => 'Checkbox',
                  ),
            array('dataIndex' => 'page_id',
                  'header'    => 'page_id',
                  'width'     => 50,
                  'hidden'   =>  true,
                  ),
            array('dataIndex' => 'id',
                  'header'    => 'id',
                  'width'     => 50,
                  'hidden'   =>  true,
                  )

            );
    protected $_buttons = array('save'=>true,
                                    'add'=>true,
                                    'delete'=>true);
    protected $_paging = 0;
    protected $_defaultOrder = 'pos';
    protected $_tableName = 'Vpc_Formular_IndexModel';

    public function indexAction()
    {
        $components = array();
        foreach (Vpc_Setup_Abstract::getAvailableComponents('Formular/') as $component) {
            if ($component != 'Vpc_Formular_Index') {
                $components[$component] = $component;
            }
        }

        $cfg = array();
        $cfg['components'] = $components;
        $this->view->ext('Vpc.Formular.Index', $cfg);
    }

    protected function _getTable()
    {
        return Zend_Registry::get('dao')->getTable('Vpc_Formular_IndexModel');
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
                $row = $this->_table->find($id)->current();
            } else {
                if(!isset($this->_permissions['add']) || !$this->_permissions['add']) {
                    throw new Vps_Exception("Add is not allowed.");
                }
                $submitRow['page_id'] = $this->component->getDbId();
                $submitRow['component_key'] = $this->component->getComponentKey();
                $submitRow['pos'] = $this->_getPosition();
                unset($submitRow['id']);
              //  d ($submitRow);
                $row = $this->_table->createRow($submitRow);
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
                   /* if ($col['type'] == 'boolean') {
                        if(isset($submitRow[$col['dataIndex']])) {
                            $val = 1;
                        } else {
                            $val = 0;
                        }
                        $row->$col['dataIndex'] = $val;
                    }*/
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

        private function _getPosition (){

    	$rows = $this->_table->fetchAll(array('page_id = ?'  => $this->component->getDbId(),
                                             'component_key = ?' => $this->component->getComponentKey()));
		$ids = array();
    	foreach ($rows as $rowKey => $rowData){
        	$id =$rowData->pos;
        	$ids[] = $id;
        }
		rsort($ids);
		if ($ids == array()) $id = 1;
		else $id = $ids[0] + 1;
		return $id;
    }

     public function jsonDeleteAction()
    {
        if(!isset($this->_permissions['delete']) || !$this->_permissions['delete']) {
            throw new Vps_Exception("Delete is not allowed.");
        }
        $success = false;
        $id = $this->getRequest()->getParam($this->_primaryKey);
		$id = $_REQUEST['id'];

        $row = $this->_table->find($id)->current();
        if(!$row) {
            throw new Vps_Exception("Can't find row with id '$id'.");
        }
        try {
            $row->delete();
            $success = true;
        } catch (Vps_ClientException $e) { //todo: nicht nur Vps_Exception fangen
            $this->view->error = $e->getMessage();
        }

        $this->view->success = $success;
    }
}