<?php
abstract class Kwc_Feedback_Form_Trl_Form_Component extends Kwc_Feedback_Form_Component
{
    protected function _beforeInsert(Kwf_Model_Row_Interface $row)
    {
        parent::_beforeInsert($row);
        $row->component_id = $this->getData()->parent->dbId;
    }
}
