<?php
class Kwc_Basic_LinkTag_Extern_ApiContent implements Kwf_Component_ApiContent_Interface
{
    public function getContent(Kwf_Component_Data $data)
    {
        $ret = array(
            'href' => $data->url,
            'rel' => $data->rel,
            'class' => $data->getLinkClass(),
            'dataAttributes' => $data->getLinkDataAttributes()
        );
        return $ret;
    }
}
