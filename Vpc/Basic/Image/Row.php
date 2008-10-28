<?php
class Vpc_Basic_Image_Row extends Vps_Model_Proxy_Row
{
    public function imageExists()
    {
        $fRow = $this->getParentRow('Image');
        if (!$fRow) return false;
        if (!file_exists($fRow->getFileSource())) return false;
        return true;
    }
}
