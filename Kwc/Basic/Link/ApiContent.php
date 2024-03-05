<?php
class Kwc_Basic_Link_ApiContent implements Kwf_Component_ApiContent_Interface
{
    public function getContent(Kwf_Component_Data $data)
    {
        return array(
            'text' => $data->getComponent()->getRow()->text,
            'link' => $data->getChildComponent('-linkTag')
        );
    }
}
