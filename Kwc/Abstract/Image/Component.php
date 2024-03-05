<?php
class Kwc_Abstract_Image_Component extends Kwc_Abstract_Composite_Component
    implements Kwf_Media_Output_IsValidInterface, Kwf_Media_Output_ClearCacheInterface
{
    const USER_SELECT = Kwf_Form_Field_Image_UploadField::USER_SELECT;
    const CONTENT_WIDTH = Kwf_Form_Field_Image_UploadField::CONTENT_WIDTH;
    private $_imageDataOrEmptyImageData;

    public static function getSettings($param = null)
    {
        $ret = parent::getSettings($param);
        $ret['ownModel'] = 'Kwc_Abstract_Image_Model';

        $ret['dimensions'] = array(
            'default'=>array(
                'width' => 300,
                'height' => 200,
                'cover' => false
              // cover = true means image will be scaled up to match size.
                //  so the aspect ratio will be fixed when croping so that scaling wont deform image
                // cover = false means image wont be scaled up if smaller than size.
            ),
            'fullWidth'=>array(
                'text' => trlKwfStatic('full width'),
                'width' => Kwf_Form_Field_Image_UploadField::CONTENT_WIDTH,
                'height' => 0,
                'cover' => true
            ),
            'original'=>array(
                'text' => trlKwfStatic('original')
            ),
            'custom'=>array(
                'text' => trlKwfStatic('user-defined'),
                'width' => Kwf_Form_Field_Image_UploadField::USER_SELECT,
                'height' => Kwf_Form_Field_Image_UploadField::USER_SELECT,
                'cover' => true
            ),
        );

        $ret['imageLabel'] = trlKwfStatic('Image');
        $ret['maxResolution'] = null;
        $ret['pdfMaxWidth'] = 0;
        $ret['pdfMaxDpi'] = 150;
        $ret['editFilename'] = true;
        $ret['imageCaption'] = false;
        $ret['altText'] = true;
        $ret['titleText'] = true;
        $ret['allowBlank'] = true;
        $ret['showHelpText'] = false;
        $ret['useDataUrl'] = false;
        $ret['lazyLoadOutOfViewport'] = true; // Set to false to load image also when not in view
        $ret['loadedAnimationClass'] = 'webImageLoadedAnimation';
        $ret['imgCssClass'] = '';
        $ret['flags']['hasFulltext'] = true;
        $ret['assetsAdmin']['dep'][] = 'KwfImageUpload';
        $ret['throwHasContentChangedOnRowColumnsUpdate'] = 'kwf_upload_id';
        $ret['outputImgTag'] = true;

        $ret['defineWidth'] = false;
        $ret['maxWidthImageWidth'] = true;
        $ret['inlineTags'] = false;
        $ret['imageCompressionQuality'] = Kwf_Config::getValue('imageCompressionQuality');
        return $ret;
    }

    public static function validateSettings($settings, $componentClass)
    {
        if (!$settings['dimensions']) {
            throw new Kwf_Exception('Dimension setting required');
        }
        if (!is_array($settings['dimensions'])) {
            throw new Kwf_Exception('Dimension setting must be an array');
        }
        foreach ($settings['dimensions'] as $k=>$d) {
            if (!is_array($d)) {
                throw new Kwf_Exception('Dimension setting must contain array of arrays');
            }
            if (isset($d['scale'])) {
                throw new Kwf_Exception('Scale does not exist anymore. Use cover = false|true instead.');
            }
        }
    }

    public function getExportData()
    {
        $ret = parent::getExportData();
        $ret['imageUrl'] = $this->getImageUrl();
        return $ret;
    }

    public function getTemplateVars(Kwf_Component_Renderer_Abstract $renderer)
    {
        $ret = parent::getTemplateVars($renderer);
        $ret['image'] = $this->getData();
        $imageCaptionSetting = $this->_getSetting('imageCaption');
        if ($imageCaptionSetting) {
            $ret['image_caption'] = $this->_getRow()->image_caption;
            $ret['showImageCaption'] = $imageCaptionSetting;
            $ret['rootElementClass'] .= ' showImageCaption';
        }

        $ret['rootElementClass'] .= ' dimension'.ucfirst($this->getDimensionSetting());
        $ret['rootElementClass'] .= ' '.$this->_getSetting('loadedAnimationClass');

        $ret['altText'] = $this->_getAltText();

        $ret = array_merge($ret, $this->_getImageOutputData());

        $ret['baseUrl'] = $this->getBaseImageUrl();
        $ret['defineWidth'] = $this->_getSetting('defineWidth');
        $ret['lazyLoadOutOfViewport'] = $this->_getSetting('lazyLoadOutOfViewport');
        $ret['outputImgTag'] = $this->_getSetting('outputImgTag');
        $ret['inlineTags'] = $this->_getSetting('inlineTags');

        $ret['style'] = '';
        $ret['captionStyle'] = '';
        if ($this->_getSetting('maxWidthImageWidth')) {
            $ret['style'] = 'max-width:'.$ret['width'].'px;';
            $ret['captionStyle'] = 'max-width:'.$ret['width'].'px;';
        }
        if ($this->_getSetting('defineWidth')) $ret['style'] .= 'width:'.$ret['width'].'px;';

        $ret['containerClass'] = $this->_getBemClass("container").' kwfUp-kwcImageContainer ';
        if (!$this->_getSetting('lazyLoadOutOfViewport')) $ret['containerClass'] .= ' kwfUp-loadImmediately';

        $ret['imgCssClass'] = $this->_getSetting('imgCssClass');
        $ret['imgAttributes'] = array();
        if ($ret['imgCssClass']) {
            $ret['imgAttributes']['class'] = $ret['imgCssClass'];
        }
        if ($titleText = $this->_getTitleText()) {
            $ret['imgAttributes']['title'] = $titleText;
        }
        return $ret;
    }

    public function getApiData()
    {
        $image = $this->_getImageOutputData();
        return array(
            'caption' => $this->_getCaptionText(),
            'alt' => $this->_getAltText(),
            'title' => $this->_getTitleText(),
            'aspectRatio' => $image['aspectRatio'],
            'widthSteps' => $image['widthSteps'],
            'baseUrl' => $this->_getAbsoluteUrl($this->getBaseImageUrl()),
            'url' => $this->getAbsoluteImageUrl(),
            'maxResolutionUrl' => $this->getMaxResolutionAbsoluteImageUrl(),
        );
    }

    private function _getImageOutputData()
    {
        $imageData = $this->getImageDataOrEmptyImageData();
        return Kwf_Media_Output_Component::getResponsiveImageVars(
            $this->getImageDimensions(), $imageData['dimensions']
        );
    }

    public function getDimensionSetting()
    {
        $dimensions = $this->_getSetting('dimensions');
        $row = $this->getRow();
        if (sizeof($dimensions) > 1) {
            if ($row && isset($dimensions[$row->dimension])) {
                return $row->dimension;
            } else {
                reset($dimensions);
                return key($dimensions);
            }
        } else {
            reset($dimensions);
            return key($dimensions);
        }
    }

    public final function getAltText()
    {
        return $this->_getAltText();
    }

    protected function _getAltText()
    {
        $ret = '';
        if ($this->_getSetting('altText')) {
            $ret = $this->_getRow()->alt_text;
        }
        return $ret;
    }

    protected function _getTitleText()
    {
        $ret = '';
        if ($this->_getSetting('titleText')) {
            $ret = $this->_getRow()->title_text;
        }
        return $ret;
    }

    protected function _getCaptionText()
    {
        $ret = '';
        if ($this->_getSetting('imageCaption')) {
            $ret = $this->_getRow()->image_caption;
        }
        return $ret;
    }

    public function hasContent()
    {
        $data = $this->_getImageDataOrEmptyImageData();
        if ($data) {
            return true;
        }
        return false;
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

    public function getAbsoluteImageUrl()
    {
        return $this->_getAbsoluteUrl($this->getImageUrl());
    }

    public function getMaxResolutionAbsoluteImageUrl()
    {
        return $this->_getAbsoluteUrl($this->getMaxResolutionImageUrl());
    }

    public function getMaxResolutionImageUrl()
    {
        $data = $this->_getImageDataOrEmptyImageData();
        if ($data) {
            $s = $this->getImageDimensions();
            $imageData = $this->_getImageDataOrEmptyImageData();
            $widths = Kwf_Media_Image::getResponsiveWidthSteps($s, $imageData['dimensions']);
            return $this->_getImageUrl(end($widths));
        }
        return null;
    }

    private function _getImageUrl($width)
    {
        if (Kwc_Abstract::getSetting($this->getData()->componentClass, 'useDataUrl')) {
            $id = $this->getData()->componentId;
            $type = str_replace('{width}', $width, $this->getBaseType());
            $data = self::getMediaOutput($id, $type, $this->getData()->componentClass);
            if (isset($data['file'])) {
                $c = file_get_contents($data['file']);
            } else {
                $c = $data['contents'];
            }
            $base64 = base64_encode($c);
            if (strlen($base64) < 32*1024) {
                $mime = $data['mimeType'];
                return "data:$mime;base64,$base64";
            }
        }
        return str_replace('{width}', $width, $this->getBaseImageUrl());
    }

    public function getImageUrl()
    {
        $data = $this->_getImageDataOrEmptyImageData();
        if ($data) {
            $s = $this->getImageDimensions();
            $imageData = $this->_getImageDataOrEmptyImageData();
            $width = Kwf_Media_Image::getResponsiveWidthStep($s['width'],
                                Kwf_Media_Image::getResponsiveWidthSteps($s, $imageData['dimensions']));
            return $this->_getImageUrl($width);
        }
        return null;
    }

    public function getBaseType()
    {
        $type = Kwf_Media::DONT_HASH_TYPE_PREFIX.'{width}';
        $type .= '-'.substr(md5(json_encode($this->_getSetting('dimensions'))), 0, 6);
        return $type;
    }

    public function getBaseImageUrl()
    {
        $data = $this->_getImageDataOrEmptyImageData();
        if ($data) {
            $ret = Kwf_Media::getUrl($this->getData()->componentClass,
                $this->getData()->componentId,
                $this->getBaseType(),
                $data['filename']);
            $ev = new Kwf_Component_Event_CreateMediaUrl($this->getData()->componentClass, $this->getData(), $ret);
            Kwf_Events_Dispatcher::fireEvent($ev);
            return $ev->url;
        }
        return null;
    }

    public function getImageData()
    {
        $row = $this->_getRow();
        $fileRow = false;
        if ($row) $fileRow = $row->getParentRow('Image');
        if (!$fileRow) return null;

        $filename = null;
        if ($this->_getSetting('editFilename')) {
            $filename = $row->filename;
        }
        if (!$filename) {
            $filename = $fileRow->filename;
        }
        $filename .= '.'.$fileRow->extension;
        $dimensions = $fileRow->getImageDimensions();
        if (!$dimensions) return null;
        return array(
            'filename' => $filename,
            'file' => $fileRow,
            'mimeType' => $fileRow->mime_type,
            'row' => $row,
            'dimensions' => $dimensions,
            'uploadId' => $fileRow->id
        );
    }

    public final function getImageDataOrEmptyImageData()
    {
        return $this->_getImageDataOrEmptyImageData();
    }

    private function _getImageDataOrEmptyImageData()
    {
        if (!isset($this->_imageDataOrEmptyImageData)) {
            $file = $this->getImageData();
            if (!$file) {
                $file = $this->_getEmptyImageData();
            }
            $this->_imageDataOrEmptyImageData = $file;
        }
        return $this->_imageDataOrEmptyImageData;
    }

    protected function _getEmptyImageData()
    {
        return null;
    }

    public function getConfiguredImageDimensions()
    {
        $row = $this->getRow();
        $dimension = $this->_getSetting('dimensions');

        $s = array();
        if (sizeof($dimension) > 1) {
            if ($row && isset($dimension[$row->dimension])) {
                $d = $dimension[$row->dimension];
            } else {
                reset($dimension);
                $d = current($dimension);
            }
        } else {
            reset($dimension);
            $d = current($dimension);
        }

        if (!isset($d['width'])) {
            $s['width'] = 0;
        } else if ($d['width'] === self::USER_SELECT) {
            if (!is_object($row)) {
                $s['width'] = 0;
            } else {
                $s['width'] = $row->width;
            }
        } else if ($d['width'] === self::CONTENT_WIDTH) {
            $s['width'] = self::CONTENT_WIDTH;
        } else {
            $s['width'] = $d['width'];
        }
        if (!isset($d['height'])) {
            $s['height'] = 0;
        } else if ($d['height'] === self::USER_SELECT) {
            if (!is_object($row)) {
                $s['height'] = 0;
            } else {
                $s['height'] = $row->height;
            }
        } else {
            $s['height'] = $d['height'];
        }
        if (isset($d['cover'])) {
            $s['cover'] = $d['cover'];
        }
        if (isset($d['aspectRatio'])) $s['aspectRatio'] = $d['aspectRatio'];

        if ($row) {
            if ($row->crop_width && $row->crop_height) {
                $s['crop']['x'] = $row->crop_x;
                $s['crop']['y'] = $row->crop_y;
                $s['crop']['width'] = $row->crop_width;
                $s['crop']['height'] = $row->crop_height;
            }
        }

        $s['imageCompressionQuality'] = $this->_getSetting('imageCompressionQuality');

        return $s;
    }

    public function getImageDimensions()
    {
        $size = $this->getConfiguredImageDimensions();
        if ($size['width'] === self::CONTENT_WIDTH) {
            $size['width'] = $this->getContentWidth();
        }
        $data = $this->_getImageDataOrEmptyImageData();
        if (isset($data['dimensions'])) {
            $size = Kwf_Media_Image::calculateScaleDimensions($data['dimensions'], $size);
        } else if (isset($data['image'])) {
            $size = Kwf_Media_Image::calculateScaleDimensions($data['image'], $size);
        } else {
            $size = Kwf_Media_Image::calculateScaleDimensions($data['file'], $size);
        }
        return $size;
    }

    public static function isValidMediaOutput($id, $type, $className)
    {
        return Kwf_Media_Output_Component::isValidImage($id, $type, $className);
    }

    public static function getMediaOutput($id, $type, $className)
    {
        $component = Kwf_Component_Data_Root::getInstance()->getComponentById($id, array('ignoreVisible' => true));
        if (!$component) return null;

        $data = $component->getComponent()->_getImageDataOrEmptyImageData();
        if (!$data) {
            return null;
        }

        $dim = $component->getComponent()->getImageDimensions();
        $data['lifetime'] = Kwf_Config::getValue('imageComponentMediaLifetimeInDays')*24*60*60;

        return Kwf_Media_Output_Component::getMediaOutputForDimension($data, $dim, $type);
    }

    public function getContentWidth()
    {
        $data = $this->_getImageDataOrEmptyImageData();
        $s = $this->getConfiguredImageDimensions();
        if ($s['width'] === self::CONTENT_WIDTH) {
            return $this->getMaxContentWidth();
        }
        if ($data) {
            if (isset($data['image'])) {
                $s = Kwf_Media_Image::calculateScaleDimensions($data['image'], $s);
            } else {
                $s = Kwf_Media_Image::calculateScaleDimensions($data['file'], $s);
            }
            return $s['width'];
        }
        return 0;
    }

    /**
     * This function is needed because getContentWidth returns the width of uploaded
     * image.
     */
    public function getMaxContentWidth()
    {
        return parent::getContentWidth();
    }

    public function getFulltextContent()
    {
        $ret = array();

        if ($this->_getSetting('imageCaption')) {
            $text = $this->_getRow()->image_caption;
            $ret['content'] = $text;
            $ret['normalContent'] = $text;
        }
        return $ret;
    }

    public static function canCacheBeDeleted($id)
    {
        return !Kwf_Component_Data_Root::getInstance()->getComponentById($id, array('ignoreVisible' => true));
    }
}
