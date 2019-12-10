<?php
class Kwc_Basic_Download_ApiContent implements Kwf_Component_ApiContent_Interface
{
    public function getContent(Kwf_Component_Data $data)
    {
        return array(
            'text' => $data->getComponent()->getRow()->infotext,
            'link' => $data->getChildComponent('-downloadTag')
        );
    }
}
