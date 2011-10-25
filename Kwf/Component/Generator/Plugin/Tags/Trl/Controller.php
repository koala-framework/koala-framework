<?php
class Kwf_Component_Generator_Plugin_Tags_Trl_Controller extends Kwf_Controller_Action_Auto_Grid
{
    protected $_buttons = array('save');

    protected function _initColumns()
    {
        $this->setModel(Kwf_Model_Abstract::getInstance('Kwf_Component_Generator_Plugin_Tags_Trl_AdminModel'));
        $this->_columns->add(new Kwf_Grid_Column('text', trlKwf('Tag'), 300))
            ->setEditor(new Kwf_Form_Field_TextField());
        $this->_columns->add(new Kwf_Grid_Column('original_text', trlKwf('Original Tag'), 300));
        parent::_initColumns();
    }
}
