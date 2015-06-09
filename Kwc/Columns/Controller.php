<?php
class Kwc_Columns_Controller extends Kwc_Abstract_List_Controller
{
    protected $_buttons = array('save', 'add', 'delete');
    protected $_position = 'pos';

    public function preDispatch()
    {
        $this->setModel(Kwc_Columns_ModelFactory::getModelInstance(array(
            'componentClass' => $this->_getParam('class')
        )));
        parent::preDispatch();
    }

    protected function _initColumns()
    {
        Kwf_Controller_Action_Auto_Kwc_Grid::_initColumns();
        $this->_columns->add(new Kwf_Grid_Column('name', trlKwf('Name'), 200));
        $this->_columns->add(new Kwf_Grid_Column_Visible());

        // Not visible
        $this->_columns->add(new Kwf_Grid_Column('total_columns'));
    }

    protected function _beforeDelete(Kwf_Model_Row_Interface $row)
    {
        parent::_beforeDelete($row);
        if ($this->_model->countRows($this->_getSelect()) <= $row->total_columns) {
            throw new Kwf_Exception_Client(trlKwf('It is not possible to delete a column in first row'));
        }
    }

    public function jsonDataAction()
    {
        $component = Kwf_Component_Data_Root::getInstance()->getComponentByDbId($this->_getParam('componentId'), array('ignoreVisible' => true));
        $row = $component->getComponent()->getRow();

        $select = new Kwf_Model_Select();
        $select->whereEquals('component_id', $this->_getParam('componentId'));

        $columnTypes = Kwc_Abstract::getSetting($this->_getParam('class'), 'columns');
        $typeName = array_shift(array_keys($columnTypes));
        if ($row && $row->type) $typeName = $row->type;
        $difference = count($columnTypes[$typeName]['colSpans']) - $this->_model->countRows($select);
        while ($difference > 0) {
            $this->_model->createRow(array(
                'component_id' => $this->_getParam('componentId'),
                'visible' => true
            ))->save();
            $difference--;
        }

        parent::jsonDataAction();
    }
}
