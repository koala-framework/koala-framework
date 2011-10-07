<?php
class Vpc_Basic_Text_Trl_Form extends Vpc_Abstract_Form
{
    protected function _initFields()
    {
        parent::_initFields();
        $this->add(Vpc_Abstract_Form::createChildComponentForm($this->getClass(), "-child", 'child'));

        $fs = $this->add(new Vps_Form_Container_FieldSet(trlVps("Original")));

        $fs->add(new Vps_Form_Field_Panel('copy'))
            ->setHideLabel(true)
            ->setXtype('vpc.basic.text.trl.copybutton');

        $fs->add(new Vps_Form_Field_ShowField('content'))
            ->setHideLabel(true)
            ->setCls('webStandard')
            ->setData(new Vps_Data_Trl_OriginalComponent());

        if (!$this->getModel()) {
            $this->setModel(new Vps_Model_FnF());
            $this->setCreateMissingRow(true);
        }
    }
}
