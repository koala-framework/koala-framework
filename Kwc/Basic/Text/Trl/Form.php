<?php
class Kwc_Basic_Text_Trl_Form extends Kwc_Abstract_Form
{
    protected function _initFields()
    {
        parent::_initFields();
        $this->add(Kwc_Abstract_Form::createChildComponentForm($this->getClass(), "-child", 'child'));

        $fs = $this->add(new Kwf_Form_Container_FieldSet(trlKwf("Original")));

        $fs->add(new Kwf_Form_Field_Panel('copy'))
            ->setHideLabel(true)
            ->setXtype('kwc.basic.text.trl.copybutton');

        $fs->add(new Kwf_Form_Field_ShowField('content'))
            ->setHideLabel(true)
            ->setCls('kwfUp-webStandard')
            ->setData(new Kwf_Data_Trl_OriginalComponent());

        if (!$this->getModel()) {
            $this->setModel(new Kwf_Model_FnF());
            $this->setCreateMissingRow(true);
        }
    }
}
