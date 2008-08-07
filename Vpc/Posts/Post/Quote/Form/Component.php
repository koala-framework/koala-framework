<?php
class Vpc_Posts_Post_Quote_Form_Component extends Vpc_Posts_Write_Form_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        return $ret;
    }

    protected function _initForm()
    {
        Vpc_Form_Component::_initForm();
        $this->_form->setTable($this->getData()->parent->parent->parent->getComponent()->getTable());
    }

    protected function _beforeSave(Vps_Model_Row_Interface $row)
    {
        $row->component_id = $this->getData()->parent->parent->parent->componentId;
    }
}
