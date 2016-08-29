<?php
class Kwc_Basic_Table_Controller extends Kwf_Controller_Action_Auto_Kwc_Grid
{
    protected $_buttons = array('save', 'add', 'delete');
    protected $_position = 'pos';

    protected function _initColumns()
    {
        $component = Kwf_Component_Data_Root::getInstance()
            ->getComponentByDbId($this->_getParam('componentId'), array('ignoreVisible'=>true, 'limit'=>1));
        $maxColumns = Kwc_Abstract::getSetting($component->componentClass, 'maxColumns');

        $sel = new Kwf_Form_Field_Select();
        $rowStyles = Kwc_Abstract::getSetting($this->_getParam('class'), 'rowStyles');
        $rowStylesSelect = array();
        foreach ($rowStyles as $k => $rowStyle) {
            $rowStylesSelect[$k] = $rowStyle['name'];
        }
        $sel->setValues($rowStylesSelect);
        $sel->setShowNoSelection(true);
        $this->_columns->add(new Kwf_Grid_Column_Visible());
        $this->_columns->add(new Kwf_Grid_Column('css_style', trlKwf('Style'), 100))
            ->setEditor($sel);
        for ($i = 1; $i <= $maxColumns; $i++) {
            $ed = new Kwf_Form_Field_TextField();
            $ed->setAllowTags(true);
            $this->_columns->add(new Kwf_Grid_Column("column$i", $this->_getColumnLetterByIndex($i-1), 150))
                ->setEditor($ed);
        }
    }
}
