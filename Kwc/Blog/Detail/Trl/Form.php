<?php
class Kwc_Blog_Detail_Trl_Form extends Kwf_Form
{
    public function __construct($name, $detailClass = null)
    {
        $this->setClass($detailClass);
        parent::__construct('details');
    }

    protected function _initFields()
    {
        parent::_initFields();

        $this->add(new Kwf_Form_Field_TextField('title', trlKwf('Title')))
            ->setAllowBlank(false)
            ->setWidth(300);
    }
}
