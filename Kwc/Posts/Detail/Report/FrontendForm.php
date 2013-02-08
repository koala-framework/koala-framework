<?php
class Kwc_Posts_Detail_Report_FrontendForm extends Kwc_Abstract_FrontendForm
{
    protected function _init()
    {
        $this->setModel(new Kwf_Model_Mail(array('componentClass' => $this->getClass())));
        $this->add(new Kwf_Form_Field_TextArea('reason', trlKwfStatic('Please enter a reason for reporting this Post')))
            ->setWidth('100%')
            ->setHeight(150)
            ->setLabelAlign('top');
        parent::_init();
    }

    protected function _beforeSave(Kwf_Model_Row_Interface $row)
    {
        $row->addTo(Kwc_Abstract::getSetting($this->getClass(), 'reportMail'));
        $row->setFrom('Report-Component');
        $row->subject = trlKwfStatic('A post has been reported');
    }
}
