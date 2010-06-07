<?php
class Vps_View_Helper_ImageUrl extends Vps_View_Helper_Image
{
    public function imageUrl($image, $type = 'default')
    {
        $data = $this->_getImageParams($image, $type);
        if (!$data) return '';
        return $data['url'];
    }
}
