<?php
class Vps_View_Helper_Image
{
    public function image($row, $rule = null, $type = 'default', $alt = '')
    {
        $url = $row->getFileUrl($rule, $type);
        $size = $row->getImageDimensions($rule, $type);
        if ($url) {
            return "<img src=\"$url\" width=\"$size[width]\" height=\"$size[height]\" alt=\"$alt\" />";
        } else {
            return '';
        }
    }
}
