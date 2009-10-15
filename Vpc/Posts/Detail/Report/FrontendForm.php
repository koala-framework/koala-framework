<?php
class Vpc_Posts_Detail_Report_FrontendForm extends Vps_Form
{
    protected function _init()
    {
        $this->setModel(new Vps_Model_Mail(array('componentClass' => $this->getClass())));
        $this->add(new Vps_Form_Field_TextArea('reason', trlVps('Please enter a reason for reporting this Post')))
            ->setWidth('100%')
            ->setHeight(150)
            ->setLabelAlign('top');
        parent::_init();
    }

    protected function _beforeSave(Vps_Model_Row_Interface $row)
    {
        $row->addTo(Vpc_Abstract::getSetting($this->getClass(), 'reportMail'));
        $row->setFrom('Report-Component');
        $row->subject = trlVps('A post has been reported');
    }
}
