<?php
class Kwc_Mail_Abstract_Trl_Component extends Kwc_Chained_Abstract_MasterAsChild_Component
{
    public function send(Kwc_Mail_Recipient_Interface $recipient, $data = null, $toAddress = null, $format = null, $addViewTracker = true)
    {
        $this->getData()->getChildComponent('-child')
            ->getComponent()
            ->send($recipient, $data, $toAddress, $format, $addViewTracker);
    }
}
