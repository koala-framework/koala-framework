<?php
class Kwc_Advanced_DownloadsTree_Data_Fileicon extends Kwf_Data_Abstract
{
    public function load($row)
    {
        $f = $row->getParentRow('File');
        if (!$f) return '';
        return (string)Kwf_Util_FileIcon::getFileIcon($f->extension);
    }
}
