<?php
class Vpc_Basic_Flash_Upload_Trl_Form extends Vpc_Abstract_Form //nicht von Vpc_Abstract_Composite_Trl_Form, da sonst die felder doppelt eingefügt werden
{
    protected function _initFields()
    {
        parent::_initFields();

        $fs = $this->add(new Vps_Form_Container_FieldSet(trlVps('Own Flash')));
        $fs->setCheckboxToggle(true);
        $fs->setCheckboxName('own_flash');
        $fs->add(Vpc_Abstract_Form::createChildComponentForm($this->getClass(), '-flash', 'flash'));
    }
}
