<?php
class Vpc_Advanced_DownloadsTree_Data_Filesize extends Vps_Data_Abstract
{
    public function load($row)
    {
        $f = $row->getParentRow('File');
        if (!$f) return '';
        return $f->getFileSize();
    }
}
