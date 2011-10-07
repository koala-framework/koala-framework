<?php
class Kwc_Advanced_Team_Member_Image_Trl_Form extends Kwc_Abstract_Image_Trl_Form
{
    protected function _initFields()
    {
        parent::_initFields();
        foreach ($this->fields as $f) {
            if ($f instanceof Kwf_Form_Container_FieldSet) {
                break;
            }
        }
        $this->fields->remove($f);
    }
}
