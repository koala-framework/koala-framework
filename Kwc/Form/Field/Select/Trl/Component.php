<?php
class Kwc_Form_Field_Select_Trl_Component extends Kwc_Form_Field_Abstract_Trl_Component
{
    public static function getSettings($masterComponentClass = null)
    {
        $ret = parent::getSettings($masterComponentClass);
        $ret['ownModel'] = 'Kwc_Form_Field_Select_Trl_Model';
        return $ret;
    }

    protected function _getFormField()
    {
        $ret = parent::_getFormField();
        $values = array();
        foreach ($this->getRow()->getChildRows('Values') as $i) {
            $values[$i->value] = $i->value;
        }
        $ret->setValues($values);
        return $ret;
    }
}
