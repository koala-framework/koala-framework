<?php
class Kwc_Basic_Table_Trl_Controller extends Kwf_Controller_Action_Auto_Kwc_Grid
{
    protected $_buttons = array('save');
    protected $_defaultOrder = 'pos';

    public function preDispatch()
    {
        $this->setModel($this->_getComponent()->getComponent()->getChildModel());
        parent::preDispatch();
    }

    protected function _initColumns()
    {
        $this->_columns->add(new Kwf_Grid_Column('pos'));
        $this->_columns->add(new Kwf_Grid_Column_Visible());
        for ($i = 1; $i <= $this->_getComponent()->chained->getComponent()->getColumnCount(); $i++) {
            $this->_columns->add(new Kwf_Grid_Column("column$i"."data"))
                ->setData(new Kwc_Basic_Table_Trl_ControllerIsTrlData("column$i"));

            $this->_columns->add(new Kwf_Grid_Column("column$i", $this->_getColumnLetterByIndex($i-1), 150))
                ->setRenderer('tableTrl')
                ->setEditor(new Kwf_Form_Field_TextField());
        }
    }

    protected function _getRowById($id)
    {
        if ($id) {
            $s = new Kwf_Model_Select();
            $s->whereEquals('master_id', $id);
            $componentId = $this->_getParam('componentId');
            $s->whereEquals('component_id', $componentId);
            $row = $this->_model->getRow($s);
        } else {
            if (!isset($this->_permissions['add']) || !$this->_permissions['add']) {
                throw new Kwf_Exception("Add is not allowed.");
            }
            $row = $this->_model->createRow();
        }
        return $row;
    }

    private function _getComponent()
    {
        return Kwf_Component_Data_Root::getInstance()
            ->getComponentById($this->_getParam('componentId'), array('ignoreVisible' => true));
    }
}

class Kwc_Basic_Table_Trl_ControllerIsTrlData extends Kwf_Data_Abstract
{
    protected $_column;
    public function __construct($column)
    {
        $this->_column = $column;
    }

    public function load($row)
    {
        return $row->getMasterValueIfNoTrl($this->_column);
    }
}
