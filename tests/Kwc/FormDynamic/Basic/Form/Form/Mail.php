<?php
class Kwc_FormDynamic_Basic_Form_Form_Mail extends Kwf_Mail
{
    public function send($transport = null)
    {
        if (!$transport) {
            $transport = new Kwc_FormDynamic_Basic_Form_Form_TestTransport();
        }
        return parent::send($transport);
    }
}
