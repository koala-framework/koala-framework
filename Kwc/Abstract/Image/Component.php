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
                'text' => trlKwf('default'),
                'width' => 300,
                'height' => 200,
                'scale' => Kwf_Media_Image::SCALE_BESTFIT
            ),
            'original'=>array(
                'text' => trlKwf('original'),
                'width' => 0,
                'height' => 0,
                'scale' => Kwf_Media_Image::SCALE_ORIGINAL
            ),
            'custombestfit'=>array(
                'text' => trlKwf('user-defined'),
                'width' => self::USER_SELECT,
                'height' => self::USER_SELECT,
                'scale' => Kwf_Media_Image::SCALE_BESTFIT
            ),
            'customcrop'=>array(
                'text' => trlKwf('user-defined'),
                'width' => self::USER_SELECT,
                'height' => self::USER_SELECT,
                'scale' => Kwf_Media_Image::SCALE_CROP
            ),
            'fullWidth'=>array(
                'text' => trlKwf('full width'),
                'width' => self::CONTENT_WIDTH,
                'height' => 0,
                'scale' => Kwf_Media_Image::SCALE_DEFORM
            ),
        );

        $ret['imageLabel'] = trlKwf('Image');
        $ret['maxResolution'] = null;
        $ret['pdfMaxWidth'] = 0;
        $ret['pdfMaxDpi'] = 150;
        $ret['editFilename'] = false;
        $ret['imageCaption'] = false;
        $ret['altText'] = true;
        $ret['allowBlank'] = true;
        $ret['showHelpText'] = false;
        $ret['useDataUrl'] = false;
        $ret['flags']['hasFulltext'] = true;
        $ret['assetsAdmin']['dep'][] = 'KwfFormFile';
        $ret['assetsAdmin']['dep'][] = 'ExtFormTriggerField';
        $ret['assetsAdmin']['files'][] = 'kwf/Kwc/Abstract/Image/DimensionField.js';
        $ret['assets']['files'][] = 'kwf/Kwc/Abstract/Image/Component.js';
        $ret['assets']['dep'][] = 'KwfOnReady';
        $ret['throwHasContentChangedOnRowColumnsUpdate'] = 'kwf_upload_id';
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
            if (!array_key_exists('width', $d)) {
                throw new Kwf_Exception('Dimension \''.$k.'\' must contain width');
            }
            if (!array_key_exists('height', $d)) {
                throw new Kwf_Exception('Dimension \''.$k.'\' must contain height');
            }
            if (!array_key_exists('scale', $d)) {
                throw new Kwf_Exception('Dimension \''.$k.'\' must contain scale');
            }
            $validScales = array(Kwf_Media_Image::SCALE_BESTFIT, Kwf_Media_Image::SCALE_CROP, Kwf_Media_Image::SCALE_ORIGINAL, Kwf_Media_Image::SCALE_DEFORM);
            if (!in_array($d['scale'], $validScales)) {
                throw new Kwf_Exception("Invalid Scale '$d[scale]' for Dimension \''.$k.'\'");
            }
            if ($d['scale'] != Kwf_Media_Image::SCALE_ORIGINAL) {
                if (!$d['width'] && !$d['height']) {
                    throw new Kwf_Exception('Dimension \''.$k.'\' must contain width or height');
                }
            }
        }

        //wenn erste dimension (=standard wert!) bestfit oder crop ist, müssen
        //width oder height gesetzt sein
        reset($settings['dimensions']);
        $firstDimension = current($settings['dimensions']);
        if (($firstDimension['scale'] === Kwf_Media_Image::SCALE_BESTFIT ||
            $firstDimension['scale'] === Kwf_Media_Image::SCALE_CROP) &&
            empty($firstDimension['width']) &&
            empty($firstDimension['height'])
        ) {
            throw new Kwf_Exception('The first dimension must contain width or height if bestfit or crop is used');
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
        }

        //image src for high device pixel ratio (retina) displays
        $ret['imageDpr2'] = null;
        $data = $this->_getImageDataOrEmptyImageData();
        if ($data) {
            if (isset($data['image'])) {
                $sourceSize = array($data['image']->getImageWidth(), $data['image']->getImageHeight());
            } else {
                $sourceSize = @getimagesize($data['file']);
            }
            $targetSize = $this->getImageDimensions();
            if ($sourceSize[0] > $targetSize['width']*1.1 || $sourceSize[1] > $targetSize['height']*1.1) {
                $id = $this->getData()->componentId;
                $type = 'dpr2-'.$this->getImageUrlType();
                $ret['imageDpr2'] = Kwf_Media::getUrl($this->getData()->componentClass, $id, $type, $data['filename']);
            }
        }

        $ret['altText'] = $this->_getAltText();

        return $ret;
    }

    protected function _getAltText()
    {
        $ret = '';
        if ($this->_getSetting('altText')) {
            $ret = $this->_getRow()->alt_text;
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

    //only for events
    public function getImageUrlType()
    {
        $type = 'default';
        $s = $this->_getImageDimensions();
        if ($s['width'] === self::CONTENT_WIDTH) {
            //use the contentWidth as type so we have an unique media cacheId depending on the width
            //that way it's not necessary to delete the media cache when content with changes
            $type = $this->getContentWidth();
        }
        return $type;
    }

    public function getImageUrl()
    {
        $data = $this->_getImageDataOrEmptyImageData();
        if ($data) {
            $id = $this->getData()->componentId;
            $type = $this->getImageUrlType();
            if (Kwc_Abstract::getSetting($this->getData()->componentClass, 'useDataUrl')) {
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
            return Kwf_Media::getUrl($this->getData()->componentClass, $id, $type, $data['filename']);
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
            'row' => $row
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

    protected function _getImageDimensions()
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
        $s['scale'] = $d['scale'];
        if (isset($d['aspectRatio'])) $s['aspectRatio'] = $d['aspectRatio'];
        return $s;
    }

    public function getImageDimensions()
    {
        $size = $this->_getImageDimensions();
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
        return Kwf_Media_Output_Component::isValid($id);
    }

    public static function getMediaOutput($id, $type, $className)
    {
        $component = Kwf_Component_Data_Root::getInstance()->getComponentById($id, array('ignoreVisible' => true));
        if (!$component) return null;

        $data = $component->getComponent()->_getImageDataOrEmptyImageData();
        if (!$data) {
            return null;
        }

        if (substr($type, 0, 5) == 'dpr2-') {
            //display pixel ratio 2
            $dim = $component->getComponent()->getImageDimensions(); //take actual image size as base
            $dim['width'] *= 2;
            $dim['height'] *= 2;
            if ($dim['scale'] === Kwf_Media_Image::SCALE_BESTFIT) {
                //don't cause rounding errors
                $dim['scale'] = Kwf_Media_Image::SCALE_DEFORM;
            }
        } else {
            //default size; display pixel ratio 1
            $dim = $component->getComponent()->_getImageDimensions();
            if ($dim['width'] === self::CONTENT_WIDTH) {
                $dim['width'] = $component->getComponent()->getContentWidth();
            }
        }
        $ret = array();
        if (isset($data['image'])) {
            $output = Kwf_Media_Image::scale($data['image'], $dim);
            $ret['contents'] = $output;
        } else {
            $sourceSize = @getimagesize($data['file']);
            $scalingNeeded = true;
            $resultingSize = Kwf_Media_Image::calculateScaleDimensions($data['file'], $dim);
            if ($sourceSize && array($resultingSize['width'], $resultingSize['height']) == array($sourceSize[0], $sourceSize[1])) {
                $scalingNeeded = false;
            }
            if ($scalingNeeded) {
                //NOTE: don't pass actual size of the resulting image, scale() will calculate that on it's own
                //else size is calculated twice and we get rounding errors
                $output = Kwf_Media_Image::scale($data['file'], $dim);
                $ret['contents'] = $output;
            } else {
                $ret['file'] = $data['file'];
            }
        }
        $ret['mimeType'] = $data['mimeType'];

        $ret['mtime'] = filemtime($data['file']);
        return $ret;
    }

    public function getContentWidth()
    {
        $data = $this->_getImageDataOrEmptyImageData();
        $s = $this->_getImageDimensions();
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
