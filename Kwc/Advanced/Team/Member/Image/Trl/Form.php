<?php
class Vpc_Advanced_Team_Member_Image_Trl_Form extends Vpc_Abstract_Image_Trl_Form
{
    protected function _initFields()
    {
        parent::_initFields();
        foreach ($this->fields as $f) {
            if ($f instanceof Vps_Form_Container_FieldSet) {
                break;
            }
        }
        $this->fields->remove($f);
    }
}
