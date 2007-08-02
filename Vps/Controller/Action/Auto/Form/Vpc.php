<?php
abstract class Vps_Controller_Action_Auto_Form_Vpc extends Vps_Controller_Action_Auto_Form {
    public function indexAction()
    {
       $this->view->ext('Vps.Auto.Form');
    }

    /*public function jsonIndexAction()
    {
    	 $info = $this->_table->info();
		$check = $info['cols'];
		d ($check);
       $this->indexAction();
    }
    public function jsonLoadAction()
    {
        $defaultSettings = $this->component->getDefaultSettings(); // Komponente ist unter $this->component zu finden
        $info = $this->_table->info();
		$check = $info['cols'];
		$newarray = array_intersect_key($defaultSettings, $check);
        $this->_table->createDefaultRow($this->_getParam('id'), $newarray);
        parent::jsonLoadAction();
    }*/

	//Override
    public function jsonIndexAction()
    {
       $info = $this->_table->info();
	   $check = $info['cols'];
       $this->indexAction();
    }

	//Override
    public function jsonLoadAction()
    {
        $defaultSettings = $this->component->getDefaultSettings(); // Komponente ist unter $this->component zu finden
        $info = $this->_table->info();
		$check = $info['cols'];
		$keys = array_keys($defaultSettings);

		//Überprüfung ob default Settingsu nd values übereinstimmen
		$values = array();
		foreach ($check AS $checkKey => $checkValue){
			if (in_array($checkValue, $keys)){
				$values[$checkValue] = $defaultSettings[$checkValue];
			}
		}
		$pageId = $this->component->getDbId();
		$componentKey = $this->component->getComponentKey();

		// Zeilie wird in der Datenbank angelegt, falls es sie noch nicht gibt
        if ($this->_table->find($pageId, $componentKey)->count() == 0) {
            $values['page_id'] = $pageId;
            $values['component_key'] = $componentKey;
            $this->_table->insert($values);
        }
      //  parent::jsonLoadAction();

      // code wurde hier ganz herein kopiert -> wegen der verschiedenen Key


        if ($pageId) {
            $row = $this->_fetchData($pageId, $componentKey);
            if (!$row) {
                throw new Vps_Exception("No database-entry with id '$id' found");
            }
            if (!$this->_hasPermissions((object)$row, 'load')) {
                throw new Vps_Exception("You don't have the permission to load id '$id'.");
            }
            if (is_array($row)) $row = (object)$row;
            $this->view->data = array();
            foreach ($this->_fields as $field) {
                if (isset($field['name'])) {
                    $name = $field['name'];
                } else if (isset($field['hiddenName'])) {
                    $name = $field['hiddenName'];
                } else {
                    $name = false;
                }
                if ($name) {
                    if (isset($field['findParent'])) {
                        $this->view->data[$name] = $this->_fetchFromParentRow($row, $field['findParent']);
                    } else {
                        $this->view->data[$name] = $this->_fetchFromRow($row, $name);
                    }
                }
            }
        }

        if ($this->getRequest()->getParam('meta')) {
            $this->_appendMetaData();
        }

    }
    //Override
    public function jsonSaveAction()
    {
        if(!isset($this->_permissions['save']) || !$this->_permissions['save']) {
            throw new Vps_Exception("Save is not allowed.");
        }

        $pageId = $this->component->getDbId();
		$componentKey = $this->component->getComponentKey();
        if ($pageId) {
            $row = $this->_table->find($pageId, $componentKey)->current();
        } else {
            if(!isset($this->_permissions['add']) || !$this->_permissions['add']) {
                throw new Vps_Exception("Add is not allowed.");
            }
            $row = $this->_table->createRow();
        }
        if(!$row) {
            throw new Vps_Exception("Can't find row with page-id '$pageId'.");
        }
        foreach ($this->_fields as $field) {
            $name = false;
            if (isset($field['name'])) $name = $field['name'];
            else if (isset($field['hiddenName'])) $name = $field['hiddenName'];
            if ($name && isset($row->$name)) {
                $row->$name = $this->getRequest()->getParam($name);
            }
        }
        $this->_beforeSave($row);
        if (!$pageId) {
            $this->_beforeInsert($row);
        }
        if ($pageId && !$this->_hasPermissions($row, 'save')) {
            throw new Vps_Exception("You don't have the permission to save id '$id'.");
        }
        $row->save();
        $this->_afterSave($row);
        if (!$pageId) {
            $this->_afterInsert($row);
            $this->view->addedId = $row->$primaryKey;
        }
    }

     protected function _fetchData($pageId, $componentKey)
    {
        if (!isset($this->_table)) {
            throw new Vps_Exception("Either _table has to be set or _fetchData has to be overwritten.");
        }
        return $this->_table->find($pageId, $componentKey)->current();
    }
}
