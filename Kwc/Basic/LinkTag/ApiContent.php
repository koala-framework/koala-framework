<?php
class Kwc_Basic_LinkTag_ApiContent implements Kwf_Component_ApiContent_Interface
{
    public function getContent(Kwf_Component_Data $data)
    {
        return $data->getChildComponent('-child');
    }
}
