<?php
class Kwc_Abstract_Image_Component extends Kwc_Abstract_Composite_Component
    implements Kwf_Media_Output_IsValidInterface
{
    const USER_SELECT = 'user';
    const CONTENT_WIDTH = 'contentWidth';
    private $_imageDataOrEmptyImageData;

    public static function getSettings()
    {
        $ret = parent::getSettings();
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
                'width' => self::CONTENT_WIDTH,
                'height' => 0,
                'cover' => true
            ),
            'original'=>array(
                'text' => trlKwfStatic('original')
            ),
            'custom'=>array(
                'text' => trlKwfStatic('user-defined'),
                'width' => self::USER_SELECT,
                'height' => self::USER_SELECT,
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
        $ret['assetsAdmin']['dep'][] = 'KwfFormFile';
        $ret['assetsAdmin']['dep'][] = 'ExtFormTriggerField';
        $ret['assetsAdmin']['files'][] = 'kwf/Kwc/Abstract/Image/DimensionField.css';
        $ret['assetsAdmin']['files'][] = 'kwf/Kwc/Abstract/Image/DimensionField.js';
        $ret['assetsAdmin']['files'][] = 'kwf/Kwc/Abstract/Image/DimensionWindow.css';
        $ret['assetsAdmin']['files'][] = 'kwf/Kwc/Abstract/Image/DimensionWindow.js';
        $ret['assetsAdmin']['files'][] = 'kwf/Kwc/Abstract/Image/CropImage.js';
        $ret['assetsAdmin']['files'][] = 'kwf/Kwc/Abstract/Image/CropImage.css';
        $ret['assetsAdmin']['files'][] = 'kwf/Kwc/Abstract/Image/ImageUploadField.js';
        $ret['assetsAdmin']['files'][] = 'kwf/Kwc/Abstract/Image/ImageUploadField.scss';
        $ret['assetsAdmin']['files'][] = 'kwf/Kwf_js/Utils/Resizable.js';
        $ret['assetsAdmin']['files'][] = 'kwf/Kwc/Abstract/Image/ImageFile.js';
        $ret['throwHasContentChangedOnRowColumnsUpdate'] = 'kwf_upload_id';

        $ret['defineWidth'] = false;
        $ret['maxWidthImageWidth'] = true;
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

    public function getTemplateVars()
    {
        $ret = parent::getTemplateVars();
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

        $imageData = $this->getImageDataOrEmptyImageData();
        $ret = array_merge($ret,
            Kwf_Media_Output_Component::getResponsiveImageVars($this->getImageDimensions(), $imageData['file'])
        );

        $ret['baseUrl'] = $this->getBaseImageUrl();
        $ret['defineWidth'] = $this->_getSetting('defineWidth');
        $ret['lazyLoadOutOfViewport'] = $this->_getSetting('lazyLoadOutOfViewport');

        $ret['style'] = '';
        $ret['captionStyle'] = '';
        if ($this->_getSetting('maxWidthImageWidth')) {
            $ret['style'] = 'max-width:'.$ret['width'].'px;';
            $ret['captionStyle'] = 'max-width:'.$ret['width'].'px;';
        }
        if ($this->_getSetting('defineWidth')) $ret['style'] .= 'width:'.$ret['width'].'px;';

        $ret['containerClass'] = $this->_getBemClass("container").' kwfUp-kwcImageContainer ';
        if ($ret['width'] > 100) $ret['containerClass'] .= ' kwfUp-webResponsiveImgLoading';
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

    public function hasContent()
    {
        $data = $this->_getImageDataOrEmptyImageData();
        if ($data) {
            return true;
        }
        return false;
    }

    public function getImageUrl()
    {
        $data = $this->_getImageDataOrEmptyImageData();
        if ($data) {
            $s = $this->getImageDimensions();
            $imageData = $this->_getImageDataOrEmptyImageData();
            $width = Kwf_Media_Image::getResponsiveWidthStep($s['width'],
                                Kwf_Media_Image::getResponsiveWidthSteps($s, $imageData['file']));
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
            return Kwf_Media::getUrl($this->getData()->componentClass,
                $this->getData()->componentId,
                $this->getBaseType(),
                $data['filename']);
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
        $file = $fileRow->getFileSource();
        if (!$file || !file_exists($file)) return null;
        return array(
            'filename' => $filename,
            'file' => $file,
            'mimeType' => $fileRow->mime_type,
            'row' => $row,
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

        return $s;
    }

    public function getImageDimensions()
    {
        $size = $this->getConfiguredImageDimensions();
        if ($size['width'] === self::CONTENT_WIDTH) {
            $size['width'] = $this->getContentWidth();
        }
        $data = $this->_getImageDataOrEmptyImageData();
        if (isset($data['image'])) {
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

        return Kwf_Media_Output_Component::getMediaOutputForDimension($data, $dim, $type);
    }

    public function getContentWidth()
    {
        $data = $this->_getImageDataOrEmptyImageData();
        $s = $this->getConfiguredImageDimensions();
        if ($s['width'] === self::CONTENT_WIDTH) {
            return parent::getContentWidth();
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
}
