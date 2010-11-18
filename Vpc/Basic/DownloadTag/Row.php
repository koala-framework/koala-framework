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

    protected function _beforeSave()
    {
        parent::_beforeSave();
        if (is_null($this->filename)) {
            $fRow = $this->getParentRow('File');
            if ($fRow) {
                $this->filename = $fRow->filename;
            }
        }
    }
}
