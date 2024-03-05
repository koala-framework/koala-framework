<?php
class Kwf_View_Helper_Image extends Kwf_Component_View_Helper_Abstract
{
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
        $url = $this->_getImageUrl($image);
        if (stripos($url, "/assets/") === 0) {
            $file = new Kwf_Assets_Dependency_File(Kwf_Assets_ProviderList_Default::getInstance(), substr($url, 8));
            return $file->getAbsoluteFileName();
        } else {
            throw new Kwf_Exception("Path does not include '/assets/'. Not implemented yet.");
        }
    }

    public function image($image, $alt = '', $attributes = null)
    {
        if (!$image) return '';

        $url = $this->_getImageUrl($image);
        if ($url == '') return '';

        if (substr($url, 0, 8) == '/assets/') {
            $subroot = null;
            if ($this->_getView() && $this->_getView()->component) {
                $subroot = $this->_getView()->component->getSubroot();
            }
            $ev = new Kwf_Events_Event_CreateAssetUrl(get_class($this), $url, $subroot);
            Kwf_Events_Dispatcher::fireEvent($ev);
            $url = $ev->url;
        }

        $class = '';
        if (is_string($attributes)) { $class = $attributes; }
        if (is_string($image)) {
            if (file_exists(str_replace('/images/', '/images/dpr2/', $this->_getAssetPath($image)))) {
                $class .= ' kwfUp-kwfReplaceImageDpr2';
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
            $attr .= ' '.Kwf_Util_HtmlSpecialChars::filter($k).'="'.Kwf_Util_HtmlSpecialChars::filter($i).'"';
        }
        return "<img src=\"" .Kwf_Util_HtmlSpecialChars::filter($url) . "\"$attr alt=\"" .Kwf_Util_HtmlSpecialChars::filter($alt) . "\" />";
    }
}
