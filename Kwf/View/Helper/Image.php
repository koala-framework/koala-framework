<?php
class Kwf_View_Helper_Image extends Kwf_View_Helper_ImageUrl
{
    public function image($image, $alt = '', $cssClass = null)
    {
        if (!$image) return '';

        $url = $this->imageUrl($image);

        $size = $this->_getImageSize($image);
        $attr = '';
        if ($cssClass && is_string($cssClass)) {
            $attr .= ' class="'.$cssClass.'"';
        } else if (is_array($cssClass)) {
            foreach ($cssClass as $k=>$i) {
                $attr .= ' '.$k.'="'.$i.'"';
            }
        }
        return "<img src=\"$url\" width=\"$size[width]\" height=\"$size[height]\" alt=\"$alt\"$attr />";
    }
}
