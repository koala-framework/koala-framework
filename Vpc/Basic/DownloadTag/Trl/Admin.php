<?php
class Vpc_Basic_DownloadTag_Trl_Admin extends Vpc_Basic_DownloadTag_Admin
{
    public function componentToString(Vps_Component_Data $data)
    {
        $r = $data->getComponent()->getRow();
        if ($r->own_download) {
            $r = $data->getChildComponent('-download')->getComponent()->getRow();
        } else  {
            $r = $data->chained->getComponent()->getRow();
        }

        if (!empty($r->filename)) return $r->filename;
        return '';
    }
}
