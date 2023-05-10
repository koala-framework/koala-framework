<?php
class Kwc_Basic_DownloadTag_Trl_ApiContent implements Kwf_Component_ApiContent_Interface
{
    public function getContent(Kwf_Component_Data $data)
    {
        if ($data->getComponent()->getRow()->own_download) {
            return $data->getChildComponent('-download');
        }
        else {
            return $data->chained;
        }
    }
}
