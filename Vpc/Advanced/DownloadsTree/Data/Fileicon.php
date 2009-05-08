<?php
class Vpc_Advanced_DownloadsTree_Data_Fileicon extends Vps_Data_Abstract
{
    public function load($row)
    {
        $f = $row->getParentRow('File');
        if (!$f) return '';
        return (string)Vps_Util_FileIcon::getFileIcon($f->extension);
    }
}
