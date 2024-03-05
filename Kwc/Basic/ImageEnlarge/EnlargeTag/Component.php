<?php
class Kwc_Basic_ImageEnlarge_EnlargeTag_Component extends Kwc_Abstract
    implements Kwf_Media_Output_IsValidInterface
{
    public static function getSettings($param = null)
    {
        $ret = parent::getSettings($param);
        $ret['componentName'] = trlKwfStatic('Enlarge Image');
        $ret['fullSizeDownloadable'] = false;
        $ret['imageTitle'] = true;
        $ret['dimension'] = array('width'=>1920, 'height'=>1440, 'cover' => false);

        $ret['generators']['imagePage'] = array(
            'class' => 'Kwf_Component_Generator_Page_Static',
            'name' => trlKwfStatic('Image'),
            'component' => 'Kwc_Basic_ImageEnlarge_EnlargeTag_ImagePage_Component'
        );

        $ret['ownModel'] = 'Kwf_Component_FieldModel';
        $ret['apiContent'] = 'Kwc_Basic_ImageEnlarge_EnlargeTag_ApiContent';
        $ret['apiContentType'] = 'imageEnlarge';

        return $ret;
    }

    /**
     * This function is used by Kwc_Basic_ImageEnlarge_EnlargeTag_ImagePage_Component
     * to get the dimension-values defined in getSettings and the crop-values
     * if use_crop was checked.
     */
    public function getImageDimensions()
    {
        $dimension = $this->_getSetting('dimension');
        if ($this->getRow()->use_crop) {
            $parentDimension = $this->_getImageEnlargeComponent()->getImageDimensions();
            $dimension['crop'] = $parentDimension['crop'];
        }
        $data = $this->getImageData();
        return Kwf_Media_Image::calculateScaleDimensions($data['file'], $dimension);
    }

    public static function validateSettings($settings, $componentClass)
    {
        parent::validateSettings($settings, $componentClass);
        if (isset($settings['showInactiveSwitchLinks'])) {
            throw new Kwf_Exception("'showInactiveSwitchLinks' setting got removed; style them using css");
        }
    }

    public function getTemplateVars(Kwf_Component_Renderer_Abstract $renderer)
    {
        $ret = parent::getTemplateVars($renderer);
        $ret['imageUrl'] = $this->getImageUrl();
        $ret['imagePage'] = $this->getData()->getChildComponent('_imagePage', array('ignoreVisible'=>true));

        $parent = $this->getData()->parent;
        if (is_instance_of($parent->componentClass, 'Kwc_Basic_LinkTag_Component')) {
            $ret['linkTitle'] = $this->getData()->parent->getComponent()->getLinkTitle();
        }
        return $ret;
    }

    protected function _getOptions()
    {
        $ret = array();
        if ($this->_getSetting('imageTitle')) {
            $ret['title'] = nl2br($this->getRow()->title);
        }
        if ($this->_getSetting('fullSizeDownloadable')) {
            $data = $this->getImageData();
            if ($data && $data['filename']) {
                $ret['fullSizeUrl'] = Kwf_Media::getUrl($this->getData()->componentClass,
                    $this->getData()->componentId, 'original', $data['filename']);
            }
        }
        return $ret;
    }

    /**
     * This function is called by Kwc_Basic_ImageEnlarge_EnlargeTag_ImagePage_Trl_Component
     */
    public final function getOptions()
    {
        return $this->_getOptions();
    }

    protected function _getImageEnlargeComponent()
    {
        $d = $this->getData();
        while (!is_instance_of($d->componentClass, 'Kwc_Basic_Image_Component')) {
            $d = $d->parent;
        }
        return $d->getComponent();
    }

    public function hasContent()
    {
        return !!$this->getImageDataOrEmptyImageData();
    }

    /**
     * This function is used by Kwc_Basic_ImageEnlarge_EnlargeTag_ImagePage_Component
     * to get the url to show the image from parent with dimension defined through
     * this component.
     */
    public function getImageUrl()
    {
        $baseUrl = $this->getBaseImageUrl();
        if ($baseUrl) {
            $dimensions = $this->getImageDimensions();
            $imageData = $this->getImageData();
            $width = Kwf_Media_Image::getResponsiveWidthStep($dimensions['width'],
                    Kwf_Media_Image::getResponsiveWidthSteps($dimensions, $imageData['file']));
            $ret = str_replace('{width}', $width, $baseUrl);
            $ev = new Kwf_Component_Event_CreateMediaUrl($this->getData()->componentClass, $this->getData(), $ret);
            Kwf_Events_Dispatcher::fireEvent($ev);
            return $ev->url;
        }
        return null;
    }

    public function getBaseType()
    {
        $type = Kwf_Media::DONT_HASH_TYPE_PREFIX.'{width}';
        $type .= '-'.substr(md5(json_encode($this->_getSetting('dimension'))), 0, 6);
        return $type;
    }

    public function getBaseImageUrl()
    {
        $data = $this->_getImageEnlargeComponent()->getImageData();
        if ($data) {
            $id = $this->getData()->componentId;
            return Kwf_Media::getUrl($this->getData()->componentClass, $id, $this->getBaseType(), $data['filename']);
        }
        return null;
    }

    public function getImageData()
    {
        return $this->_getImageEnlargeComponent()->getImageData();
    }

    public final function getImageDataOrEmptyImageData()
    {
        return $this->_getImageEnlargeComponent()->getImageDataOrEmptyImageData();
    }

    public static function isValidMediaOutput($id, $type, $className)
    {
        if ($type == 'original') {
            return Kwf_Media_Output_Component::isValid($id);
        } else {
            return Kwf_Media_Output_Component::isValidImage($id, $type, $className);
        }
    }

    public static function getMediaOutput($id, $type, $className)
    {
        $component = Kwf_Component_Data_Root::getInstance()->getComponentById($id, array('ignoreVisible' => true));
        if (!$component) return null;

        $data = $component->getComponent()->getImageData();
        if (!$data) {
            return null;
        }
        if ($type == 'original') {
            return array(
                'file' => $data['file'],
                'downloadFilename' => $data['filename'],
                'mimeType' => $data['mimeType'],
            );
        } else {
            $dimension = $component->getComponent()->getImageDimensions();
            return Kwf_Media_Output_Component::getMediaOutputForDimension($data, $dimension, $type);
        }
    }

    public function getFulltextContent()
    {
        $ret = array();

        //don't call parent, we handle imageCaption ourself
        $options = (object)$this->_getOptions();
        $text = '';
        if (isset($options->title) && $options->title) {
            $text .= ' '.$options->title;
        }
        if (isset($options->imageCaption) && $options->imageCaption) {
            $text .= ' '.$options->imageCaption;
        }
        $text = trim($text);
        $ret['content'] = $text;
        $ret['normalContent'] = $text;
        return $ret;
    }

    public function getApiData()
    {
        $image = $this->_getImageOutputData();
        if (!$this->getData()->hasContent()) {
            return null;
        }
        $ret = array(
            'aspectRatio' => $image['aspectRatio'],
            'widthSteps' => $image['widthSteps'],
            'baseUrl' => $this->_getAbsoluteUrl($this->getBaseImageUrl()),
            'url' => $this->_getAbsoluteUrl($this->getImageUrl()),
            'maxResolutionUrl' => $this->_getAbsoluteUrl($this->getMaxResolutionImageUrl()),
        );
        $baseImageComponentData = $this->_getImageEnlargeComponent()->getApiData();
        if (isset($baseImageComponentData['caption']) && $baseImageComponentData['caption']) {
            $ret['caption'] = $baseImageComponentData['caption'];
        }
        if (isset($baseImageComponentData['alt']) && $baseImageComponentData['alt']) {
            $ret['alt'] = $baseImageComponentData['alt'];
        }
        if ($this->_getSetting('imageTitle')) {
            if ($this->getRow()->title) {
                $ret['title'] = $this->getRow()->title;
            } else if (isset($baseImageComponentData['title'])) {
                $ret['title'] = $baseImageComponentData['title'];
            }
        }
        return $ret;
    }

    private function _getImageOutputData()
    {
        $imageData = $this->getImageDataOrEmptyImageData();
        return Kwf_Media_Output_Component::getResponsiveImageVars(
            $this->getImageDimensions(), $imageData['dimensions']
        );
    }

    private function _getAbsoluteUrl($url)
    {
        if ($url && substr($url, 0, 1) == '/' && substr($url, 0, 2) != '//') { //can already be absolute, due to Event_CreateMediaUrl (eg. varnish cache)
            $domain = $this->getData()->getDomain();
            $protocol = Kwf_Util_Https::domainSupportsHttps($domain) ? 'https' : 'http';
            $url = "$protocol://$domain$url";
        }
        return $url;
    }

    private function getMaxResolutionImageUrl()
    {
        $imageData = $this->getImageDataOrEmptyImageData();
        if ($imageData) {
            $s = $this->getImageDimensions();
            $widths = Kwf_Media_Image::getResponsiveWidthSteps($s, $imageData['dimensions']);
            return $this->getImageUrl();
        }
        return null;
    }
}
