<?php
class Kwf_Form_FieldSet_Frontend_TestForm_Component extends Kwc_Form_Component
{
    public static function getSettings($param = null)
    {
        $ret = parent::getSettings($param);
        return $ret;
    }
    protected function _initForm()
    {
        $this->_form = new Kwf_Form();
        $this->_form->setModel(new Kwf_Model_FnF());
        $this->_form->add(new Kwf_Form_Field_TextField('foo1', 'Foo1'))
            ->setAllowBlank(false);
        $fs = $this->_form->add(new Kwf_Form_Container_FieldSet('Bar'));
            $fs->setCheckboxToggle(true);
            $fs->setCheckboxName('bar');
            $fs->add(new Kwf_Form_Field_TextField('foo2', 'Foo2'))
                ->setAllowBlank(false);
    }
}
