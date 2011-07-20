<?php
class Vps_View_Helper_Image extends Vps_Component_View_Helper_Abstract
{
    private $_dep;

    protected function _getImageUrl($image)
    {
        $url = (string)$image;
        $url = str_replace(VPS_PATH, '/assets/vps', $url);
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
        $loader = new Vps_Assets_Loader();
        return $loader->getFileContents($this->_getAssetPath($image));
    }

    private function _getAssetPath($image)
    {
        $url = $this->_getImageUrl($image);
        if (stripos($url, "/assets/") === 0) {
            if (!$this->_dep) {
                $loader = new Vps_Assets_Loader();
                $this->_dep = $loader->getDependencies();
            }
            return $this->_dep->getAssetPath(substr($url, 8));
        } else {
            throw new Vps_Exception("Path does not include '/assets/'. Not implemented yet.");
        }
    }

    protected function _getMailInterface()
    {
        return $this->_getView();
    }

    public function image($image, $alt = '', $cssClass = null)
    {
        if (!$image) return '';

        $url = $this->_getImageUrl($image);
        if ($url == '') return '';

        if ($this->_getMailInterface() instanceof Vps_View_MailInterface) {
            if ($this->_getMailInterface()->getAttachImages()) {
                $contents = $this->_getImageFileContents($image);
                $img = new Zend_Mime_Part($contents['contents']);
                $img->type = $contents['mimeType'];
                $img->disposition = Zend_Mime::DISPOSITION_INLINE;
                $img->encoding = Zend_Mime::ENCODING_BASE64;
                $img->filename = substr(strrchr($url, '/'), 1); //filename wird gesucht
                $img->id = md5($url);
                $this->_getMailInterface()->addImage($img);
                $url = "cid:".$img->id;
            }
        }

        $size = $this->_getImageSize($image);
        $attr = '';
        if (is_string($cssClass)) {
            $attr .= ' class="'.$cssClass.'"';
        } else if (is_array($cssClass)) {
            foreach ($cssClass as $k=>$i) {
                $attr .= ' '.$k.'="'.$i.'"';
            }
        }
        return "<img src=\"$url\" width=\"$size[width]\" height=\"$size[height]\" alt=\"$alt\"$attr />";
    }
}
