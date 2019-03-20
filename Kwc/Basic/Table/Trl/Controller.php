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

            $ed = new Kwf_Form_Field_TextField();
            $ed->setAllowTags(true);
            $this->_columns->add(new Kwf_Grid_Column("column$i", $this->_getColumnLetterByIndex($i-1), 150))
                ->setRenderer('tableTrl')
                ->setEditor($ed);
        }
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

    public function load($row, array $info = array())
    {
        return $row->getMasterValueIfNoTrl($this->_column);
    }
}
