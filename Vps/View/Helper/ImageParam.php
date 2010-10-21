<?php
class Vps_View_Helper_ImageParam extends Vps_View_Helper_Image
{
    public function imageParam($image, $param, $type = 'default')
    {
        $data = $this->_getImageParams($image, $type);
        if (!$data) return '';
        return $data[$param];
    }
}
