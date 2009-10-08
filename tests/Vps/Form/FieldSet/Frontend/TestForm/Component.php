<?php
class Vps_Form_FieldSet_Frontend_TestForm_Component extends Vpc_Form_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        return $ret;
    }
    protected function _initForm()
    {
        $this->_form = new Vps_Form();
        $this->_form->setModel(new Vps_Model_FnF());
        $this->_form->add(new Vps_Form_Field_TextField('foo1', 'Foo1'))
            ->setAllowBlank(false);
        $fs = $this->_form->add(new Vps_Form_Container_FieldSet('Bar'));
            $fs->setCheckboxToggle(true);
            $fs->setCheckboxName('bar');
            $fs->add(new Vps_Form_Field_TextField('foo2', 'Foo2'))
                ->setAllowBlank(false);
    }
}
