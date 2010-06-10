<?php
class Vpc_Basic_Table_Trl_Controller extends Vps_Controller_Action_Auto_Vpc_Grid
{
    protected $_buttons = array('save');
    protected $_defaultOrder = 'pos';

    public function preDispatch()
    {
        $this->setModel($this->_getComponent()->getComponent()->getChildModel());
        parent::preDispatch();
    }

    protected function _beforeSave($row)
    {
    }

    protected function _initColumns()
    {
        $this->_columns->add(new Vps_Grid_Column('pos'));
        for ($i = 1; $i <= $this->_getComponent()->chained->getComponent()->getColumnCount(); $i++) {
            $this->_columns->add(new Vps_Grid_Column("column$i", trlVps('Column {0}', $i), 150))
                ->setEditor(new Vps_Form_Field_TextField());
        }
        $this->_columns->add(new Vps_Grid_Column_Visible());
    }

    private function _getComponent()
    {
        return Vps_Component_Data_Root::getInstance()
            ->getComponentById($this->_getParam('componentId'), array('ignoreVisible' => true));
    }
}
