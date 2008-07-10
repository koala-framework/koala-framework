<?php
class Vps_View_Helper_Image
{
    public function image($row, $rule = null, $type = 'default', $alt = '', $cssClass = null)
    {
        if (!$row) return '';
        $url = $row->getFileUrl($rule, $type);
        $attr = '';
        if (is_string($cssClass)) {
            $attr .= ' class="'.$cssClass.'"';
        } else if (is_array($cssClass)) {
            foreach ($cssClass as $k=>$i) {
                $attr .= ' '.$k.'="'.$i.'"';
            }
        }
        $size = $row->getImageDimensions($rule, $type);
        if ($url) {
            return "<img src=\"$url\" width=\"$size[width]\" height=\"$size[height]\" alt=\"$alt\"$attr />";
        } else {
            return '';
        }
    }
}
