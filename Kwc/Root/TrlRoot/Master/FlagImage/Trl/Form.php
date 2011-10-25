<?php
class Kwc_Root_TrlRoot_Master_FlagImage_Trl_Form extends Kwc_Abstract_Form
{
    protected function _initFields()
    {
        parent::_initFields();
        $this->add(Kwc_Abstract_Form::createChildComponentForm($this->getClass(), "-image"));
        if (!$this->getModel()) {
            $this->setModel(new Kwf_Model_FnF());
            $this->setCreateMissingRow(true);
        }
    }
}
