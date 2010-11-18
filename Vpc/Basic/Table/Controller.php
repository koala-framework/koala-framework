<?php
class Vpc_Basic_Table_Controller extends Vps_Controller_Action_Auto_Vpc_Grid
{
    protected $_buttons = array('save', 'add', 'delete');
    protected $_model = 'Vpc_Basic_Table_ModelData';
    protected $_position = 'pos';

    protected function _initColumns()
    {
        $columnCount = Vps_Component_Data_Root::getInstance()
            ->getComponentById($this->_getParam('componentId'), array('ignoreVisible'=>true))
            ->getComponent()->getColumnCount();

        $sel = new Vps_Form_Field_Select();
        $rowStyles = Vpc_Abstract::getSetting($this->_getParam('class'), 'rowStyles');
        $rowStylesSelect = array();
        foreach ($rowStyles as $k => $rowStyle) {
            $rowStylesSelect[$k] = $rowStyle['name'];
        }
        $sel->setValues($rowStylesSelect);
        $sel->setShowNoSelection(true);
        $this->_columns->add(new Vps_Grid_Column('css_style', trlVps('Style'), 100))
            ->setEditor($sel);
        for ($i = 1; $i <= $columnCount; $i++) {
            $this->_columns->add(new Vps_Grid_Column("column$i", trlVps('Column {0}', $i), 150))
                ->setEditor(new Vps_Form_Field_TextField());
        }
        $this->_columns->add(new Vps_Grid_Column_Visible());
    }
}
