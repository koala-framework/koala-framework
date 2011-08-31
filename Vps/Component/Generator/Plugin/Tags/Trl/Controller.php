<?php
class Vps_Component_Generator_Plugin_Tags_Trl_Controller extends Vps_Controller_Action_Auto_Grid
{
    protected $_buttons = array('save');

    protected function _initColumns()
    {
        $this->setModel(Vps_Model_Abstract::getInstance('Vps_Component_Generator_Plugin_Tags_Trl_AdminModel'));
        $this->_columns->add(new Vps_Grid_Column('text', trlVps('Tag'), 300))
            ->setEditor(new Vps_Form_Field_TextField());
        $this->_columns->add(new Vps_Grid_Column('original_text', trlVps('Original Tag'), 300));
        parent::_initColumns();
    }
}
