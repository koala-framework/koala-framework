<?php
abstract class Vps_Controller_Action_Auto_AssignGrid extends Vps_Controller_Action_Auto_Grid
{
    protected $_textAssignField = null;

    private function _getAssignModel()
    {
        return Vps_Model_Abstract::getInstance($this->_modelName);
    }

    public function jsonAssignAction()
    {
        $this->_checkNecessaryProperties();

        $ids = Zend_Json::decode($this->_getParam('foreign_keys'));
        if (!count($ids)) throw new Vps_ClientException(trlVps("There's no row selected"));

        $assignModel = $this->_getAssignModel();

        $assignToRef = $assignModel->getReference($this->_assignToReference);
        $assignToColumn = $assignToRef['column'];
        $assignFromRef = $assignModel->getReference($this->_assignFromReference);
        $assignFromColumn = $assignFromRef['column'];

        ignore_user_abort(true);
        $this->_model->getAdapter()->beginTransaction();
        foreach ($ids as $id) {
            $row = $assignModel->getRow($assignModel->select()
                ->whereEquals($assignToColumn, $this->_getParam($assignToColumn))
                ->whereEquals($assignFromColumn, $id)
            );

            if (!$row) {
                $row = $assignModel->createRow();
                $row->$assignToColumn = $this->_getParam($assignToColumn);
                $row->$assignFromColumn = $id;
                $row->save();
            }
        }
        $this->_model->getAdapter()->commit();
    }

    public function jsonTextAssignAction()
    {
        $this->_checkNecessaryProperties();
        if (!$this->_textAssignField) {
            throw new Vps_Exception('$this->_textAssignField not set');
        }

        $text = $this->_getParam('assignText');
        if (!trim($text)) {
            throw new Vps_ClientException('Textinput was empty');
        }

        $assignModel = $this->_getAssignModel();

        $assignToRef = $assignModel->getReference($this->_assignToReference);
        $assignToColumn = $assignToRef['column'];
        $assignFromRef = $assignModel->getReference($this->_assignFromReference);
        $assignFromColumn = $assignFromRef['column'];

        $dataModel = Vps_Model_Abstract::getInstance($assignFromRef['refModelClass']);

        ignore_user_abort(true);
        $this->_model->getAdapter()->beginTransaction();
        $items = preg_split("(\n|\r)", $text);
        foreach ($items as $item) {
            $item = trim($item);
            if (!$item) continue;

            $dataRow = $dataModel->getRow($dataModel->select()
                ->whereEquals($this->_textAssignField, $item)
            );
            if (!$dataRow) {
                $dataRow = $dataModel->createRow(array(
                    $this->_textAssignField => $item
                ));
                $dataRow->save();
            }

            if ($dataRow) {
                $assignRow = $assignModel->getRow($assignModel->select()
                    ->whereEquals($assignToColumn, $this->_getParam($assignToColumn))
                    ->whereEquals($assignFromColumn, $dataRow->id)
                );
                if (!$assignRow) {
                    $row = $assignModel->createRow();
                    $row->$assignToColumn = $this->_getParam($assignToColumn);
                    $row->$assignFromColumn = $dataRow->id;
                    $row->save();
                }
            }
        }
        $this->_model->getAdapter()->commit();
    }

    protected function _checkNecessaryProperties()
    {
        if (!$this->_modelName) {
            throw new Vps_Exception('$this->_modelName not set');
        }
        if (!$this->_assignToReference) {
            throw new Vps_Exception('$this->_assignToReference not set');
        }
        if (!$this->_assignFromReference) {
            throw new Vps_Exception('$this->_assignFromReference not set');
        }
    }
}
