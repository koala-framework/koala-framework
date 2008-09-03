<?php
class Vpc_Posts_Post_Report_Form extends Vpc_Abstract_Form
{
    protected function _init()
    {
        $this->setModel(new Vps_Model_Mail(array('componentClass' => $this->getClass())));
        $this->add(new Vps_Form_Field_TextArea('reason', trlVps('Please enter a reason for reporting this Post')))
            ->setWidth(475)->setHeight(150);
        parent::_init();
    }

    protected function _beforeSave(Vps_Model_Row_Interface $row)
    {
        $row->htmlReason = nl2br($row->reason);
        $row->addTo(Vpc_Abstract::getSetting($this->getName(), 'reportMail'));
        $row->setFrom('Report-Component');
        $row->subject = trlVps('A post has been reported');
    }
    
}
