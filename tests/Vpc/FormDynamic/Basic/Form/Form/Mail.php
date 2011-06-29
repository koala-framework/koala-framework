<?php
class Vpc_FormDynamic_Basic_Form_Form_Mail extends Vps_Mail
{
    public function send($transport = null)
    {
        if (!$transport) {
            $transport = new Vpc_FormDynamic_Basic_Form_Form_TestTransport();
        }
        return parent::send($transport);
    }
}
