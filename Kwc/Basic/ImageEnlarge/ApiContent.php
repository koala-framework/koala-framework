<?php
class Kwc_Basic_ImageEnlarge_ApiContent implements Kwf_Component_ApiContent_Interface
{
    public function getContent(Kwf_Component_Data $data)
    {
        $imageApiContent = new Kwc_Basic_Image_ApiContent();
        return array(
            "link" => $data->getChildComponent('-linkTag'),
            "content" => array(
                "type" => "image",
                "id" => "no_id",
                "data" => $imageApiContent->getContent($data)
            )
        );
    }
}
