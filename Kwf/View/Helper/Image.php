<?php
class Kwf_View_Helper_Image extends Kwf_Component_View_Helper_Abstract
{
    private $_dep;

    protected function _getImageUrl($image)
    {
        $url = (string)$image;
        $url = str_replace(KWF_PATH, '/assets/kwf', $url);
        $url = str_replace(getcwd(), '/assets', $url);
        return $url;
    }

    protected function _getImageSize($image)
    {
        $size = getimagesize($this->_getAssetPath($image));
        $size['width'] = $size[0];
        $size['height'] = $size[1];
        return $size;
    }

    protected function _getImageFileContents($image)
    {
        $loader = new Kwf_Assets_Loader();
        return $loader->getFileContents($this->_getAssetPath($image));
    }

    private function _getAssetPath($image)
    {
        $url = $this->_getImageUrl($image);
        if (stripos($url, "/assets/") === 0) {
            if (!$this->_dep) {
                $loader = new Kwf_Assets_Loader();
                $this->_dep = $loader->getDependencies();
            }
            return $this->_dep->getAssetPath(substr($url, 8));
        } else {
            throw new Kwf_Exception("Path does not include '/assets/'. Not implemented yet.");
        }
    }

    public function image($image, $alt = '', $cssClass = null)
    {
        if (!$image) return '';

        $url = $this->_getImageUrl($image);
        if ($url == '') return '';
        if ($this->_getRenderer() instanceof Kwf_View_MailInterface &&
            substr($url, 0, 1) == '/'
        ) {
            $url = 'http://'.Kwf_Config::getValue('server.domain') . $url;
        }

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
