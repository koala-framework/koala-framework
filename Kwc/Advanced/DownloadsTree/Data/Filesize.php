<?php
class Kwc_Advanced_DownloadsTree_Data_Filesize extends Kwf_Data_Abstract
{
    public function load($row)
    {
        $f = $row->getParentRow('File');
        if (!$f) return '';
        return $f->getFileSize();
    }
}
