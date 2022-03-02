<?php
class Kwc_Basic_Image_Trl_ApiContent implements Kwf_Component_ApiContent_Interface
{
    public function getContent(Kwf_Component_Data $data)
    {
        if (!$data->hasContent()) {
            return null;
        }
        return $data->getComponent()->getApiData();
    }
}
