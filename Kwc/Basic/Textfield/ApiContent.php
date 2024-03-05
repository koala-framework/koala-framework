<?php
class Kwc_Basic_Textfield_ApiContent implements Kwf_Component_ApiContent_Interface
{
    public function getContent(Kwf_Component_Data $data)
    {
        $row = $data->getComponent()->getRow();
        return array(
            'text' => $row->content
        );
    }
}
