<?php
class Kwc_Basic_Image_ApiContent implements Kwf_Component_ApiContent_Interface
{
    public function getContent(Kwf_Component_Data $data)
    {
        if (!$data->hasContent()) {
            return new stdClass();
        }
        return $data->getComponent()->getApiData();
    }
}
