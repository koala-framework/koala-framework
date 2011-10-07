<?php
class Vpc_Advanced_DownloadsTree_Data_Filename extends Vps_Data_Abstract
{
    public function load($row)
    {
        $f = $row->getParentRow('File');
        if (!$f) return '';
        return $f->filename.'.'.$f->extension;
    }
}

