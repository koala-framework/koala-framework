<?php
class Kwf_View_Helper_Image extends Kwf_Component_View_Helper_Abstract
{
    private $_dep;

    protected function _getImageUrl($image)
    {
        $url = (string)$image;
        $url = str_replace(KWF_PATH, '/assets/kwf', $url);
        $url = str_replace(getcwd(), '/assets', $url);

        if ($this->_getMailInterface() instanceof Kwf_View_MailInterface &&
            substr($url, 0, 1) == '/'
        ) {
            $domain = Kwf_Config::getValue('server.domain');
            if (!is_string($image)) {
                $data = $image;
                while ($data && !Kwc_Abstract::getFlag($data->componentClass, 'hasDomain')) {
                    $data = $data->parent;
                }
                if ($data) {
                    $domain = $data->getComponent()->getDomain();
                }
            }
            $url = "http://$domain$url";
        }

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
        if (!$this->_dep) {
            $loader = new Kwf_Assets_Loader();
            $this->_dep = $loader->getDependencies();
        }
        $url = $this->_getImageUrl($image);
        if (stripos($url, 'http://'.Kwf_Config::getValue('server.domain')."/assets/") === 0) {
            $url = substr($url, strlen('http://'.Kwf_Config::getValue('server.domain')));
            return $this->_dep->getAssetPath(substr($url, 8));
        } else if (stripos($url, "/assets/") === 0) {
            return $this->_dep->getAssetPath(substr($url, 8));
        } else {
            throw new Kwf_Exception("Path does not include '/assets/'. Not implemented yet.");
        }
    }

    protected function _getMailInterface()
    {
        return $this->_getView();
    }

    public function image($image, $alt = '', $attributes = null)
    {
        if (!$image) return '';

        $url = $this->_getImageUrl($image);
        if ($url == '') return '';

        if ($this->_getMailInterface() instanceof Kwf_View_MailInterface) {
            if ($this->_getMailInterface()->getAttachImages()) {
                $contents = $this->_getImageFileContents($image);
                if (!isset($contents['contents'])) $contents['contents'] = file_get_contents($contents['file']);
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

        if ($this->_getRenderer() instanceof Kwf_View_MailInterface &&
            substr($url, 0, 1) == '/'
        ) {
            $url = 'http://'.Kwf_Config::getValue('server.domain') . $url;
        } else if (Kwf_Config::getValue('assetsCacheUrl') && substr($url, 0, 8) == '/assets/') {
            $url = Kwf_Config::getValue('assetsCacheUrl').'?web='.Kwf_Config::getValue('application.id')
                .'&section='.Kwf_Setup::getConfigSection()
                .'&url='.substr($url, 1);
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

        $attr = '';
        foreach ($attributes as $k=>$i) {
            $attr .= ' '.$k.'="'.$i.'"';
        }
        $size = $this->_getImageSize($image);
        return "<img src=\"$url\" width=\"$size[width]\" height=\"$size[height]\" alt=\"$alt\"$attr />";
    }
}
