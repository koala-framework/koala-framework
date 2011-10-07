<?php
class Kwc_Basic_DownloadTag_Row extends Kwf_Model_Proxy_Row
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
                $filter = new Kwf_Filter_Ascii();
                $this->filename = $filter->filter($fRow->filename);
            }
        }
    }
}
