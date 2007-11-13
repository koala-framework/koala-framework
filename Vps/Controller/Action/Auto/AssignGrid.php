<?php
class Vps_Controller_Action_Auto_AssignGrid extends Vps_Controller_Action_Auto_Grid
{

    protected $_assignTable = null;

    public function jsonAssignAction()
    {
        $this->_checkNecessaryProperties();

        $ids = Zend_Json::decode($this->_getParam('foreign_keys'));
        if (!count($ids)) throw new Vps_ClientException("There's no row selected");

        $this->_assignTable = new $this->_tableName();

        $assignToColumns = $this->_getAssignColumns('To');
        $assignFromColumns = $this->_getAssignColumns('From');

        // where vorbereiten für suche ob bereits zugewiesen
        $where = array();
        foreach ($assignToColumns as $toColumn) {
            $where["$toColumn = ?"] = $this->_getParam($toColumn);
        }

        foreach ($ids as $id) {
            foreach ($assignFromColumns as $fromColumn) {
                $where["$fromColumn = ?"] = $id;
            }
            $row = $this->_assignTable->fetchRow($where);

            if (!$row) {
                $data = array();
                foreach ($assignToColumns as $toColumn) {
                    $data[$toColumn] = $this->_getParam($toColumn);
                }
                foreach ($assignFromColumns as $fromColumn) {
                    $data[$fromColumn] = $id;
                }
                $this->_assignTable->insert($data);
            }
        }
    }

    public function jsonTextAssignAction()
    {
        $this->_checkNecessaryProperties();

        $text = $this->_getParam('assignText');
        if (!trim($text)) {
            throw new Vps_ClientException('Textinput was empty');
        }

        $this->_assignTable = new $this->_tableName();
        $refMap = $this->_getRefMap($this->_assignTable);

        $assignToColumns = $this->_getAssignColumns('To');
        $assignFromColumns = $this->_getAssignColumns('From');

        $dataTable = new $refMap[$this->_assignFromReference]['refTableClass']();

        $items = preg_split("(\n|\r)", $text);
        foreach ($items as $item) {
            $item = trim($item);
            if (!$item) continue;

            $dataRow = $dataTable->fetchRow(array("{$this->_textAssignField} = ?" => $item));
            if (!$dataRow) {
                $insertId = $dataTable->insert(array($this->_textAssignField => $item));

                $dataRow = $dataTable->fetchRow(array(
                    $refMap[$this->_assignFromReference]['refColumns'].' = ?' => $insertId
                ));
            }

            if ($dataRow) {
                $dataWhere = array();
                foreach ($assignToColumns as $toColumn) {
                    $dataWhere["$toColumn = ?"] = $this->_getParam($toColumn);
                }
                foreach ($assignFromColumns as $fromColumn) {
                    $dataWhere["$fromColumn = ?"] = $dataRow->id;
                }
                $assignRow = $this->_assignTable->fetchRow($dataWhere);
                if (!$assignRow) {
                    $insData = array();
                    foreach ($assignToColumns as $toColumn) {
                        $insData[$toColumn] = $this->_getParam($toColumn);
                    }
                    foreach ($assignFromColumns as $fromColumn) {
                        $insData[$fromColumn] = $dataRow->id;
                    }
                    $this->_assignTable->insert($insData);
                }
            }
        }
    }

    protected function _checkNecessaryProperties()
    {
        if (!$this->_tableName) {
            throw new Vps_Exception('$this->_tableName not set');
        }
        if (!$this->_assignToReference) {
            throw new Vps_Exception('$this->_assignToReference not set');
        }
        if (!$this->_assignFromReference) {
            throw new Vps_Exception('$this->_assignFromReference not set');
        }
    }

    protected function _getRefMap($model)
    {
        $refMap = $model->info();
        return $refMap['referenceMap'];
    }

    protected function _getAssignColumns($fromOrTo)
    {
        $assignReference = "_assign{$fromOrTo}Reference";

        $refMap = $this->_getRefMap($this->_assignTable);

        if (!$refMap[$this->$assignReference]) throw new Vps_Exception('$this->'.$assignReference.' does not exist');
        $assignColumns = $refMap[$this->$assignReference]['columns'];
        if (!is_array($assignColumns)) $assignColumns = array($assignColumns);
        return $assignColumns;
    }

}

