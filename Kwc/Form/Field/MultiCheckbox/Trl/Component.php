<?php
class Kwc_Form_Field_MultiCheckbox_Trl_Component extends Kwc_Form_Field_Abstract_Trl_Component
{
    public static function getSettings($masterComponentClass = null)
    {
        $ret = parent::getSettings($masterComponentClass);
        $ret['ownModel'] = 'Kwc_Form_Field_MultiCheckbox_Trl_Model';
        return $ret;
    }

    protected function _getFormField()
    {
        $ret = parent::_getFormField();
        $values = array();
        $filter = new Kwf_Filter_Ascii();
        foreach ($this->getRow()->getChildRows('Values') as $i) {
            $values[$filter->filter($i->value)] = $i->value;
        }
        $ret->setValues($values);
        return $ret;
    }

    public function getSubmitMessage($row)
    {
        $message = '';
        if ($this->getFormField()->getFieldLabel()) {
            $message .= $this->getFormField()->getFieldLabel().': ';
        }

        $values = array();
        foreach ($row->getChildRows($this->getFormField()->getName()) as $r) {
            $values[] = $r->value_id;
        }
        $valuesText = array();
        foreach ($this->getFormField()->getValues() as $k=>$i) {
            if (in_array($k, $values)) {
                $valuesText[] = $i;
            }
        }
        $message .= implode(', ', $valuesText);

        return $message;
    }
}

