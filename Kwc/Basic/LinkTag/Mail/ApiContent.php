<?php
class Kwc_Basic_LinkTag_Mail_ApiContent implements Kwf_Component_ApiContent_Interface
{
    public function getContent(Kwf_Component_Data $data)
    {
        $row = $data->getComponent()->getRow();
        return array(
            'recipient' => $row->mail,
            'subject' => $row->subject,
            'text' => $row->text
        );
    }
}
