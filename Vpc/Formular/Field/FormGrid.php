<?php
class Vpc_Formular_Field_FormGrid extends Vps_Controller_Action_Auto_Grid
{

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
                 $pageId = $this->component->getDbId();
				 $componentKey = $this->component->getComponentKey();

                $row = $this->_table->fetchall(array('page_id = ?' => $pageId,
													 'component_key = ?' => $componentKey))->current();
            } else {
                if(!isset($this->_permissions['add']) || !$this->_permissions['add']) {
                    throw new Vps_Exception("Add is not allowed.");
                }
				$submitRow['page_id'] = $this->component->getDbId();

				if ($this->component instanceof Vpc_Formular_Multicheckbox_Index){
					$componentKey = $this->_generateComponentKey($this->component->getComponentKey());
				} else {
					$componentKey = $this->component->getComponentKey();
				}

        		$submitRow['component_key'] = $componentKey;
        		if (!$this->component instanceof Vpc_Formular_Multicheckbox_Index)
        		unset($submitRow['id']);
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
                $addedIds[] = $row->page_id;
            }
        }
        $success = true;

        if ($addedIds) {
            $this->view->addedIds = $addedIds;
        }
        $this->view->success = $success;
    }

    private function _generateComponentKey ($componentKey){

    	$rows = $this->_table->fetchAll(array('page_id = ?'  => $this->component->getDbId(),
                                             'component_key LIKE ?' => "$componentKey-%"));
		$ids = array();
    	foreach ($rows as $rowKey => $rowData){
        	$id = substr(strrchr($rowData->component_key, '-'), 1);
        	$ids[] = $id;
        }
		rsort($ids);
		if ($ids == array()) $id = 1;
		else $id = $ids[0] + 1;
		return "$componentKey-$id";
    }

     public function jsonDeleteAction()
    {
        if(!isset($this->_permissions['delete']) || !$this->_permissions['delete']) {
            throw new Vps_Exception("Delete is not allowed.");
        }
        $success = false;
        $id = $this->getRequest()->getParam($this->_primaryKey);

        $row = $this->_table->find($this->component->getDbId(), $id)->current();
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