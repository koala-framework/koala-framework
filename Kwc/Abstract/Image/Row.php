<?php
class Kwc_Abstract_Image_Row extends Kwf_Model_Proxy_Row
{
    public function __toString()
    {
        $fRow = $this->getParentRow('Image');
        if (!$fRow) return '';
        return $fRow->filename;
    }

    public function imageExists()
    {
        $fRow = $this->getParentRow('Image');
        if (!$fRow) return false;
        if (!file_exists($fRow->getFileSource())) return false;
        return true;
    }
}
