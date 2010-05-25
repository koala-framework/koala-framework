<?php
class Vpc_Basic_Table_Controller extends Vps_Controller_Action_Auto_Vpc_Grid
{
    protected $_buttons = array('save', 'add', 'delete');
    protected $_model = 'Vpc_Basic_Table_ModelData';
    protected $_position = 'pos';

    protected function _initColumns()
    {
        $row = Vpc_Abstract::createOwnModel($this->_getClass())
            ->getRow($this->_getComponentId());
        if (!$row || !$row->columns) {
            throw new Vps_ClientException("Please set first the amount of columns in the settings section.");
        }

        $sel = new Vps_Form_Field_Select();
        $rowStyles = Vpc_Abstract::getSetting($this->_getClass(), 'rowStyles');
        $rowStylesSelect = array();
        foreach ($rowStyles as $k => $rowStyle) {
            $rowStylesSelect[$k] = $rowStyle['name'];
        }
        $sel->setValues($rowStylesSelect);
        $sel->setShowNoSelection(true);
        $this->_columns->add(new Vps_Grid_Column('css_style', trlVps('Style'), 100))
            ->setEditor($sel);

        for ($i = 1; $i <= $row->columns; $i++) {
            $this->_columns->add(new Vps_Grid_Column("column$i", trlVps('Column {0}', $i), 150))
                ->setEditor(new Vps_Form_Field_TextField());
        }
    }

    protected function _getClass()
    {
        return $this->_getParam('class');
    }

    protected function _getComponentId()
    {
        return $this->_getParam('componentId');
    }
}
