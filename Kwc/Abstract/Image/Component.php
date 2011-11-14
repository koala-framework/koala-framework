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
        $ret['allowBlank'] = true;
        $ret['showHelpText'] = false;
        $ret['assetsAdmin']['dep'][] = 'KwfFormFile';
        $ret['assetsAdmin']['dep'][] = 'ExtFormTriggerField';
        $ret['assetsAdmin']['files'][] = 'kwf/Kwc/Abstract/Image/DimensionField.js';
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
        if (($firstDimension['scale'] == Kwf_Media_Image::SCALE_BESTFIT ||
            $firstDimension['scale'] == Kwf_Media_Image::SCALE_CROP) &&
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
            $id = $this->getData()->componentId;
            return Kwf_Media::getUrl($this->getData()->componentClass, $id, 'default', $data['filename']);
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
        } else if ($d['width'] == self::USER_SELECT) {
            if (!is_object($row)) {
                $s['width'] = 0;
            } else {
                $s['width'] = $row->width;
            }
        } else if ($d['width'] == self::CONTENT_WIDTH) {
            $s['width'] = self::CONTENT_WIDTH;
        } else {
            $s['width'] = $d['width'];
        }
        if (!isset($d['height'])) {
            $s['height'] = 0;
        } else if ($d['height'] == self::USER_SELECT) {
            if (!is_object($row)) {
                $s['height'] = 0;
            } else {
                $s['height'] = $row->height;
            }
        } else {
            $s['height'] = $d['height'];
        }
        $s['scale'] = $d['scale'];
        return $s;
    }

    public function getImageDimensions()
    {
        $data = $this->_getImageDataOrEmptyImageData();
        $s = $this->_getImageDimensions();
        if ($s['width'] == self::CONTENT_WIDTH) {
            $s['width'] = $this->getContentWidth();
        }

        if ($data) {
            if (isset($data['image'])) {
                $s = Kwf_Media_Image::calculateScaleDimensions($data['image'], $s);
            } else {
                $s = Kwf_Media_Image::calculateScaleDimensions($data['file'], $s);
            }
        }
        return $s;
    }

    public static function isValidMediaOutput($id, $type, $className)
    {
        if (Kwf_Component_Data_Root::getInstance()->getComponentById($id)) {
            return self::VALID;
        }
        if (Kwf_Registry::get('config')->showInvisible) {
            //preview im frontend
            if (Kwf_Component_Data_Root::getInstance()->getComponentById($id, array('ignoreVisible'=>true))) {
                return self::VALID_DONT_CACHE;
            }
        }

        //paragraphs vorschau im backend
        if (Kwf_Component_Data_Root::getInstance()->getComponentById($id, array('ignoreVisible'=>true))) {
            $authData = Kwf_Registry::get('userModel')->getAuthedUser();
            if (Kwf_Registry::get('acl')->isAllowedComponentById($id, $className, $authData)) {
                return self::VALID_DONT_CACHE;
            }
        }

        return self::INVALID;
    }

    public static function getMediaOutput($id, $type, $className)
    {
        $component = Kwf_Component_Data_Root::getInstance()->getComponentById($id, array('ignoreVisible' => true));
        if (!$component) return null;

        $data = $component->getComponent()->_getImageDataOrEmptyImageData();
        if (!$data) {
            return null;
        }

        $dim = $component->getComponent()->_getImageDimensions();
        if ($dim['width'] == self::CONTENT_WIDTH) {
            $dim['width'] = $component->getComponent()->getContentWidth();
        }
        $ret = array();
        if (isset($data['image'])) {
            $output = Kwf_Media_Image::scale($data['image'], $dim);
            $ret['contents'] = $output;
        } else {
            $size = Kwf_Media_Image::calculateScaleDimensions($data['file'], $dim);
            $sourceSize = @getimagesize($data['file']);
            $scalingNeeded = (bool)$dim;
            if ($scalingNeeded && $sourceSize && array($size['width'], $size['height']) == array($sourceSize[0], $sourceSize[1])) {
                $scalingNeeded = false;
            }
            if ($scalingNeeded) {
                $output = Kwf_Media_Image::scale($data['file'], $dim);
                $ret['contents'] = $output;
            } else {
                $ret['file'] = $data['file'];
            }
        }
        $ret['mimeType'] = $data['mimeType'];

        if (Kwf_Registry::get('config')->debug->componentCache->checkComponentModification) {
            $mtimeFiles = array();
            $mtimeFiles[] = $data['file'];
            $classes = Kwc_Abstract::getParentClasses($className);
            $classes[] = $className;
            $incPaths = explode(PATH_SEPARATOR, get_include_path());
            foreach ($classes as $c) {
                $file = str_replace('_', DIRECTORY_SEPARATOR, $c);
                foreach ($incPaths as $incPath) {
                    if (file_exists($incPath.DIRECTORY_SEPARATOR.$file . '.php')) {
                        $mtimeFiles[] = $incPath.DIRECTORY_SEPARATOR.$file . '.php';
                    }
                }
            }
            $mtime = 0;
            foreach ($mtimeFiles as $f) {
                $mtime = max($mtime, filemtime($f));
            }
            $ret['mtime'] = $mtime;
            $ret['mtimeFiles'] = $mtimeFiles;
        } else {
            $ret['mtime'] = filemtime($data['file']);
        }
        return $ret;
    }

    public function getContentWidth()
    {
        $data = $this->_getImageDataOrEmptyImageData();
        $s = $this->_getImageDimensions();
        if ($s['width'] == self::CONTENT_WIDTH) {
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
}
