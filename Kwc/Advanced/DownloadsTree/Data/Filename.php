<?php
class Kwc_Advanced_DownloadsTree_Data_Filename extends Kwf_Data_Abstract
{
    public function load($row)
    {
        $f = $row->getParentRow('File');
        if (!$f) return '';
        return $f->filename.'.'.$f->extension;
    }
}

