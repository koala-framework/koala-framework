<?php
abstract class Vps_Db_Table_Row_Abstract extends Zend_Db_Table_Row_Abstract
{
    private $_skipFilters = false; //für saveSkipFilters

    public function duplicate($data = array())
    {
        $data = array_merge($this->toArray(), $data);
        unset($data['id']);
        $new = $this->getTable()->createRow($data);
        $new->save();
        return $new;
    }

    // Übersetzt Mysql-Datum in Timestamp
    public function getTimestamp($columnName)
    {
        $parts = explode('-', $this->$columnName);
        if ($parts == array("")) return null; //Bugfix, falls kein Datum vorhanden
        return mktime(0, 0, 0, $parts[1], $parts[2], $parts[0]);
    }

    protected function _duplicateParentRow($tableClassname, $ruleKey = null)
    {
        $row = $this->findParentRow($tableClassname, $ruleKey);
        $new = $row->duplicate();
        $ref = $this->getTable()->getReference($tableClassname, $ruleKey);
        $data = array();
        foreach ($ref['columns'] as $k=>$c) {
            $this->$c = $new->{$ref['refColumns'][$k]};
        }
        $this->save();
    }

    protected function _duplicateDependentTable($tableClassname, $ruleKey = null)
    {
        $rowset = $this->findDependentRowset($tableClassname, $ruleKey);
        foreach ($rowset as $row) {
            $ref = $row->getTable()->getReference($tableClassname, $ruleKey);
            $data = array();
            foreach ($ref['columns'] as $k=>$c) {
                $data[$ref['refColumns'][$k]] = $this->$c;
            }
            $row->duplicate($data);
        }
    }

    private function _getIdString()
    {
        return implode(',', $this->_getPrimaryKey());
    }


    public function toDebug()
    {
        $i = get_class($this);
        if (method_exists($this, '__toString')) {
            $i .= " (".$this->__toString().")\n";
        }
        $ret = print_r($this->_data, true);
        $ret = preg_replace('#^Array#', $i, $ret);
        $ret = "<pre>$ret</pre>";
        return $ret;
    }

    //für Filter_Row_UniqueAscii
    public function getPrimaryKey()
    {
        return $this->_getPrimaryKey();
    }

    protected function _insert()
    {
        parent::_insert();
        $this->_updateFilters();
    }

    protected function _update()
    {
        parent::_update();
        $this->_updateFilters();
    }

    private function _updateFilters($filterAfterSave = false)
    {
        if ($this->_skipFilters) return; //für saveSkipFilters

        $filters = $this->getTable()->getFilters();
        foreach($filters as $k=>$f) {
            if ($f instanceof Vps_Filter_Row_Abstract) {
                if ($f->skipFilter($this)) continue;
                if ($f->filterAfterSave() != $filterAfterSave) continue;
                $this->$k = $f->filter($this);
            } else {
                $this->$k = $f->filter($this->__toString());
            }
            if ($filterAfterSave) {
                $this->_skipFilters = true;
                $this->save();
            }
        }
    }

    public function save()
    {
        $ret = parent::save();
        $this->_updateFilters(true);
        return $ret;
    }

    protected function _delete()
    {
        parent::_delete();
        $filters = $this->getTable()->getFilters();
        foreach($filters as $k=>$f) {
            if ($f instanceof Vps_Filter_Row_Abstract) {
                $f->onDeleteRow($this);
            }
        }
    }

    //Speichern und abei die Filter nicht verwenden
    //wird benötigt bei der Nummerierung um eine Endlusschleife zu verhindern
    public function saveSkipFilters()
    {
        $this->_skipFilters = true;
        $this->save();
        $this->_skipFilters = false;
    }


    //Überschrieben als workaround für einen bug
    //http://framework.zend.com/issues/browse/ZF-3347
    public function findParentRow($parentTable, $ruleKey = null, Zend_Db_Table_Select $select = null)
    {
        $db = $this->_getTable()->getAdapter();

        //***kopiert von Zend_Db_Table_Row
        if (is_string($parentTable)) {
            try {
                Zend_Loader::loadClass($parentTable);
            } catch (Zend_Exception $e) {
                require_once 'Zend/Db/Table/Row/Exception.php';
                throw new Zend_Db_Table_Row_Exception($e->getMessage());
            }
            $parentTable = new $parentTable(array('db' => $db));
        }
        if (! $parentTable instanceof Zend_Db_Table_Abstract) {
            $type = gettype($parentTable);
            if ($type == 'object') {
                $type = get_class($parentTable);
            }
            require_once 'Zend/Db/Table/Row/Exception.php';
            throw new Zend_Db_Table_Row_Exception("Parent table must be a Zend_Db_Table_Abstract, but it is $type");
        }

        $map = $this->_prepareReference($this->_getTable(), $parentTable, $ruleKey);
        //********

        for ($i = 0; $i < count($map[Zend_Db_Table_Abstract::COLUMNS]); ++$i) {
            $dependentColumnName = $db->foldCase($map[Zend_Db_Table_Abstract::COLUMNS][$i]);
            $value = $this->_data[$dependentColumnName];
            if (!$value) {
                return null;
            }
        }

        return parent::findParentRow($parentTable, $ruleKey, $select);
    }

    protected function _postUpdate()
    {
        parent::_postUpdate();
        if (Vps_Component_Data_Root::getComponentClass()) {
            Vps_Component_ModelObserver::getInstance()->add('update', $this);
        }
    }

    protected function _postInsert()
    {
        parent::_postInsert();
        if (Vps_Component_Data_Root::getComponentClass()) {
            Vps_Component_ModelObserver::getInstance()->add('insert', $this);
        }
    }

    protected function _postDelete()
    {
        parent::_postDelete();
        if (Vps_Component_Data_Root::getComponentClass()) {
            Vps_Component_ModelObserver::getInstance()->add('delete', $this);
        }
    }

    /**
     * @deprecated in neuerem vps haben auch die Vps_Models dieses feature, das verwenden!
     * das hier ist nur da weil es bei rssinclude benötigt wurde
     */
    public function ___getDirtyColumns()
    {
        return array_keys($this->_modifiedFields);
    }

}
