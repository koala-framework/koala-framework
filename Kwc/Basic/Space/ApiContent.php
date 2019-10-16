<?php
class Kwc_Basic_Space_ApiContent implements Kwf_Component_ApiContent_Interface
{
    public function getContent(Kwf_Component_Data $data)
    {
        return array(
            'height' => $data->getComponent()->getRow()->height
        );
    }
}
