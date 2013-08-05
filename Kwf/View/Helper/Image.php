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

    private function _getAssetPath($image)
    {
        if (!$this->_dep) {
            $loader = new Kwf_Assets_Loader();
            $this->_dep = $loader->getDependencies();
        }
        $url = $this->_getImageUrl($image);
        if (stripos($url, "/assets/") === 0) {
            return $this->_dep->getAssetPath(substr($url, 8));
        } else {
            throw new Kwf_Exception("Path does not include '/assets/'. Not implemented yet.");
        }
    }

    public function image($image, $alt = '', $attributes = null)
    {
        if (!$image) return '';

        $url = $this->_getImageUrl($image);
        if ($url == '') return '';

        if (Kwf_Config::getValue('assetsCacheUrl') && substr($url, 0, 8) == '/assets/') {
            $url = Kwf_Config::getValue('assetsCacheUrl').'?web='.Kwf_Config::getValue('application.id')
                .'&section='.Kwf_Setup::getConfigSection()
                .'&url='.substr($url, 1);
        } else if (Kwf_Setup::getBaseUrl() && substr($url, 0, 8) == '/assets/') {
            $url = Kwf_Setup::getBaseUrl().$url;
        }

        $class = '';
        if (is_string($attributes)) { $class = $attributes; }
        if (is_string($image)) {
            if (file_exists(str_replace('/images/', '/images/dpr2/', $this->_getAssetPath($image)))) {
                $class .= ' kwfReplaceImageDpr2';
            }
        }
        $class = trim($class);

        if (!is_array($attributes)) { $attributes = array(); }
        if ($class != '') { $attributes['class'] = $class; }

        $size = $this->_getImageSize($image);
        if (!isset($attributes['width'])) $attributes['width'] = $size['width'];
        if (!isset($attributes['height'])) $attributes['height'] = $size['height'];

        $attr = '';
        foreach ($attributes as $k=>$i) {
            $attr .= ' '.$k.'="'.$i.'"';
        }
        return "<img src=\"$url\"$attr alt=\"$alt\" />";
    }
}
