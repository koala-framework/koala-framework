<?php
class Vps_View_Helper_Image
{
    private $_view;
    private $_dep;
    public function setView($view)
    {
        $this->_view = $view;
    }

    public function image($image, $type = 'default', $alt = '', $cssClass = null)
    {
        if (is_string($image)) {
            $cssClass = $alt;
            $alt = $type;
        }
        $attr = '';
        if (is_string($cssClass)) {
            $attr .= ' class="'.$cssClass.'"';
        } else if (is_array($cssClass)) {
            foreach ($cssClass as $k=>$i) {
                $attr .= ' '.$k.'="'.$i.'"';
            }
        }

        if ($image instanceof Vpc_Abstract_Image_Component) {
            $image = $image->getData();
        }
        if (!$image) {
            $url = false;
        } else if (is_string($image)){
            $image = str_replace(VPS_PATH, '/assets/vps', $image);
            $image = str_replace(getcwd(), '/assets', $image);
            $url = $image;
            if (!$this->_dep) {
                $this->_dep = new Vps_Assets_Dependencies();
            }
            if (stripos($url, "/assets/") === 0) {
                $depUrl = substr($url, 8);
            } else {
                throw new Vps_Exception("Path does not include '/assets/'. Not implemented yet.");
            }
            $size = getimagesize($this->_dep->getAssetPath($depUrl)); //image
            $size['width'] = $size[0];
            $size['height'] = $size[1];
        } else if ($image instanceof Vps_Component_Data) {
            $c = $image->getComponent();
            if (!$c instanceof Vpc_Abstract_Image_Component) {
                throw new Vps_Exception("No Vpc_Abstract_Image_Component Component given (is '".get_class($c)."')");
            }
            $url = $c->getImageUrl();
            $size = $c->getImageDimensions();
        } else {
            throw new Vps_Exception("Invalid image argument");
        }

        if ($url) {
            //bei vps_view_mail soll das image als attachment hinzugefügt werden
            if ($this->_view instanceof Vps_View_Mail) {
                if (is_string($image)){
                    $fileContents = $this->_dep->getFileContents($depUrl);
                    $mimeType = $fileContents['mimeType'];
                    $content = $fileContents['contents'];
                } else {
                    $content = file_get_contents($row->getFileSource($rule, $type));
                    $mimeType = $row->getFileRow($rule)->mime_type;
                }
                $img = new Zend_Mime_Part($content);
                $img->type = $mimeType;
                $img->disposition = Zend_Mime::DISPOSITION_INLINE;
                $img->encoding = Zend_Mime::ENCODING_BASE64;
                $img->filename = substr(strrchr($url, '/'), 1); //filename wird gesucht
                $img->id = Vps_View_Mail::getCid($url);
                $this->_view->addImage($img);
                $url = "cid:".$img->id;
            }
            return "<img src=\"$url\" width=\"$size[width]\" height=\"$size[height]\" alt=\"$alt\"$attr />";
        } else {
            return '';
        }
    }
}
