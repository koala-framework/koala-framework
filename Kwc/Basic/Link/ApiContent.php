<?php
class Kwc_Basic_Link_ApiContent implements Kwf_Component_ApiContent_Interface
{
    public function getContent(Kwf_Component_Data $data)
    {
        $linkTagData = $data->getChildComponent('-linkTag');
        $ret = array(
            'text' => $data->getComponent()->getRow()->text,
            'href' => $linkTagData->url,
            'rel' => $linkTagData->rel,
            'class' => $linkTagData->getLinkClass(),
            'dataAttributes' => $linkTagData->getLinkDataAttributes()
        );
        return $ret;
    }
}
