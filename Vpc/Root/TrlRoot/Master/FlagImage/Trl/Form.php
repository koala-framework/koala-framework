<?php
class Vpc_Root_TrlRoot_Master_FlagImage_Trl_Form extends Vpc_Abstract_Form
{
    protected function _initFields()
    {
        parent::_initFields();
        $this->add(Vpc_Abstract_Form::createChildComponentForm($this->getClass(), "-image"));
        if (!$this->getModel()) {
            $this->setModel(new Vps_Model_FnF());
            $this->setCreateMissingRow(true);
        }
    }
}
