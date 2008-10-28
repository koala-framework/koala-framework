<?php
class Vpc_Basic_DownloadTag_Row extends Vps_Model_Proxy_Row
{
    public function fileExists()
    {
        $fRow = $this->getParentRow('File');
        if (!$fRow) return false;
        if (!file_exists($fRow->getFileSource())) return false;
        return true;
    }
}
