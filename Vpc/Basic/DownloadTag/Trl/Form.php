<?php
class Vpc_Basic_DownloadTag_Trl_Form extends Vpc_Abstract_Form
{
    protected function _initFields()
    {
        parent::_initFields();
        $fs = $this->add(new Vps_Form_Container_FieldSet(trlVps('Own Download')));
        $fs->setCheckboxToggle(true);
        $fs->setCheckboxName('own_download');
        $fs->add(Vpc_Abstract_Form::createChildComponentForm($this->getClass(), '-download', 'download'));
    }
}
