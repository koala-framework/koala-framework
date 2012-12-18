<?php
class Kwc_Basic_Flash_Upload_Trl_Form extends Kwc_Abstract_Form //nicht von Kwc_Abstract_Composite_Trl_Form, da sonst die felder doppelt eingefügt werden
{
    protected function _initFields()
    {
        parent::_initFields();

        $fs = $this->add(new Kwf_Form_Container_FieldSet(trlKwf('Own Flash')));
        $fs->setCheckboxToggle(true);
        $fs->setCheckboxName('own_flash');
        $fs->add(Kwc_Abstract_Form::createChildComponentForm($this->getClass(), '-flash', 'flash'));
    }
}
