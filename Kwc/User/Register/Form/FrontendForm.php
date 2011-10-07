<?php
class Vpc_User_Register_Form_FrontendForm extends Vpc_User_Edit_Form_FrontendForm
{
    public function initFields()
    {
        parent::initFields();
        $this->_setHidden($this);
    }

    //TODO: sollte mit Vps_Collection_Iterator_Recursive einfacher funktionieren
    private function _setHidden($f)
    {
        if ($f->getHideInRegister()) {
            $this->_setHidden2($f);
        }
        foreach ($f as $i) {
            if (is_object($i)) {
                $this->_setHidden($i);
            }
        }
    }
    private function _setHidden2($f)
    {
        $f->setHidden(true);
        $f->setSave(false);
        foreach ($f as $i) {
            if (is_object($i)) {
                $this->_setHidden2($i);
            }
        }
    }
}
