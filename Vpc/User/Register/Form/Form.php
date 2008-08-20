<?php
class Vpc_User_Register_Form_Form extends Vpc_User_Edit_Form_Form
{
    public function initFields()
    {
        parent::initFields();
        $this->fields['email']->addValidator(new Vpc_User_Register_Form_ValidateEMail());
        $this->_setHidden($this);
    }

    //TODO: sollte mit Vps_Collection_Iterator_Recursive einfacher funktionieren
    private function _setHidden($f)
    {
        if ($f->getHideInRegister()) {
            $f->setHidden(true);
        }
        foreach ($f as $i) {
            if (is_object($i)) {
                $this->_setHidden($i);
            }
        }
    }
}
