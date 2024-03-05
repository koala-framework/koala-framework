<?php
class Kwc_Basic_ImageEnlarge_EnlargeTag_ApiContent implements Kwf_Component_ApiContent_Interface
{
    public function getContent(Kwf_Component_Data $data)
    {
        return array(
            "image" => array(
                "type" => "image",
                "id" => "no_id",
                "data" => $data->getComponent()->getApiData()
            )
        );
    }
}
