<?php
class Vpc_Advanced_Amazon_Nodes_Controller extends Vps_Controller_Action_Auto_Vpc_Grid
{
    protected $_position = 'pos';
    protected $_buttons = array('save', 'add', 'delete');

    protected function _initColumns()
    {
        $this->_columns->add(new Vps_Grid_Column('name', trlVps('Name')))
            ->setEditor(new Vps_Form_Field_TextField());
        $this->_columns->add(new Vps_Grid_Column('node_id', trlVps('Node-ID')))
            ->setEditor(new Vps_Form_Field_TextField());
        $this->_columns->add(new Vps_Grid_Column_Visible());
    }
}
