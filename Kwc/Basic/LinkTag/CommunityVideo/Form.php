<?php
class Kwc_Basic_LinkTag_CommunityVideo_Form extends Kwc_Abstract_Form
{
    public function __construct($name, $class)
    {
        parent::__construct($name, $class);
        if (!$this->getModel()) {
            $this->setModel(new Kwf_Model_FnF());
            $this->setCreateMissingRow(true);
        }
    }

    protected function _initFields()
    {
        parent::_initFields();
        $form = Kwc_Abstract_Form::createChildComponentForm($this->getClass(), "_video", 'video');
        $form->setIdTemplate('{0}_video');
        $this->add($form);
    }
}
