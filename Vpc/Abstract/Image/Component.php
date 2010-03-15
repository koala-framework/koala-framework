<?php
class Vpc_Abstract_Image_Component extends Vpc_Abstract_Composite_Component
    implements Vps_Media_Output_Interface
{
    const USER_SELECT = 'user';
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['ownModel'] = 'Vpc_Abstract_Image_Model';

        $ret['dimensions'] = array(
            'default'=>array(
                'text' => trlVps('default'),
                'width' => 300,
                'height' => 200,
                'scale' => Vps_Media_Image::SCALE_BESTFIT
            ),
            'original'=>array(
                'text' => trlVps('original'),
                'width' => 0,
                'height' => 0,
                'scale' => Vps_Media_Image::SCALE_ORIGINAL
            ),
            'custombestfit'=>array(
                'text' => trlVps('user-defined'),
                'width' => self::USER_SELECT,
                'height' => self::USER_SELECT,
                'scale' => Vps_Media_Image::SCALE_BESTFIT
            ),
            'customcrop'=>array(
                'text' => trlVps('user-defined'),
                'width' => self::USER_SELECT,
                'height' => self::USER_SELECT,
                'scale' => Vps_Media_Image::SCALE_CROP
            ),
        );

        $ret['pdfMaxWidth'] = 0;
        $ret['pdfMaxDpi'] = 150;
        $ret['editFilename'] = false;
        $ret['allowBlank'] = true;
        $ret['showHelpText'] = false;
        $ret['assetsAdmin']['dep'][] = 'VpsSwfUpload';
        $ret['assetsAdmin']['dep'][] = 'ExtFormTriggerField';
        $ret['assetsAdmin']['files'][] = 'vps/Vpc/Abstract/Image/DimensionField.js';
        return $ret;
    }

    public static function validateSettings($settings, $componentClass)
    {
        if (!$settings['dimensions']) {
            throw new Vps_Exception('Dimension setting required');
        }
        if (!is_array($settings['dimensions'])) {
            throw new Vps_Exception('Dimension setting must be an array');
        }
        foreach ($settings['dimensions'] as $d) {
            if (!is_array($d)) {
                throw new Vps_Exception('Dimension setting must contain array of arrays');
            }
            if (!array_key_exists('width', $d)) {
                throw new Vps_Exception('Dimension setting must contain width');
            }
            if (!array_key_exists('height', $d)) {
                throw new Vps_Exception('Dimension setting must contain height');
            }
            if (!array_key_exists('scale', $d)) {
                throw new Vps_Exception('Dimension setting must contain scale');
            }
            $validScales = array(Vps_Media_Image::SCALE_BESTFIT, Vps_Media_Image::SCALE_CROP, Vps_Media_Image::SCALE_ORIGINAL, Vps_Media_Image::SCALE_DEFORM);
            if (!in_array($d['scale'], $validScales)) {
                throw new Vps_Exception("Invalid Scale '$d[scale]'");
            }
        }

        reset($settings['dimensions']);
        $firstDimension = current($settings['dimensions']);
        if (($firstDimension['scale'] == Vps_Media_Image::SCALE_BESTFIT ||
            $firstDimension['scale'] == Vps_Media_Image::SCALE_CROP) &&
            empty($firstDimension['width']) &&
            empty($firstDimension['height'])
        ) {
            throw new Vps_Exception('The first dimension must contain width or height if bestfit or crop is used');
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
        return $ret;
    }

    public function hasContent()
    {
        $data = $this->_getImageDataOrEmptyImageData();
        if ($data && $data['file'] && file_exists($data['file'])) {
            return true;
        }
        return false;
    }

    public function getImageUrl()
    {
        $data = $this->_getImageDataOrEmptyImageData();
        if ($data && $data['filename']) {
            $id = $this->getData()->componentId;
            return Vps_Media::getUrl(get_class($this), $id, 'default', $data['filename']);
        }
        return null;
    }

    public function getImageData()
    {
        $row = $this->_getRow();
        $fileRow = false;
        if ($row) $fileRow = $row->getParentRow('Image');

        $filename = null;
        if ($fileRow) {
            $filename = $row->filename;
            if (!$filename) {
                $filename = $fileRow->filename;
            }
            $filename .= '.'.$fileRow->extension;
        }
        return array(
            'filename' => $filename,
            'file' => $fileRow ? $fileRow->getFileSource() : null,
            'mimeType' => $fileRow ? $fileRow->mime_type : null,
            'row' => $row
        );
    }

    private function _getImageDataOrEmptyImageData()
    {
        $file = $this->getImageData();
        if (!$file['file']) {
            $file = $this->_getEmptyImageData();
        }
        return $file;
    }

    public function _getCacheRow()
    {
        $data = $this->getImageData();
        if (isset($data['row'])) return $data['row'];
        return null;
    }

    protected function _getEmptyImageData()
    {
        return null;
    }

    public function getImageDimensions()
    {
        $data = $this->_getImageDataOrEmptyImageData();
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

        if ($data && $data['file'] && file_exists($data['file'])) {
            $sourceSize = @getimagesize($data['file']);
            if (!$sourceSize) return null;
            return Vps_Media_Image::calculateScaleDimensions($sourceSize, $s);
        }
        return $s;
    }

    public static function getMediaOutput($id, $type, $className)
    {
        $component = Vps_Component_Data_Root::getInstance()->getComponentById($id, array('ignoreVisible' => true));
        if (!$component) return null;

        $data = $component->getComponent()->_getImageDataOrEmptyImageData();
        if (!$data || !$data['file'] || !file_exists($data['file'])) {
            return null;
        }

        $dim = $component->getComponent()->getImageDimensions();
        if ($dim) {
            $output = Vps_Media_Image::scale($data['file'], $dim);
        } else {
            $output = file_get_contents($data['file']);
        }
        $ret = array(
            'contents' => $output,
            'mimeType' => $data['mimeType'],
        );
        if (Vps_Registry::get('config')->debug->componentCache->checkComponentModification) {
            $mtimeFiles = array();
            $mtimeFiles[] = $data['file'];
            $classes = Vpc_Abstract::getParentClasses($className);
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
        if (isset($data['row'])) {
            Vps_Component_Cache::getInstance()->saveMeta(
                get_class($data['row']->getModel()), $data['row']->component_id, $id, Vps_Component_Cache::META_CALLBACK
            );
        }
        return $ret;
    }

    public function onCacheCallback($row)
    {
        $cacheId = Vps_Media::createCacheId(
            $this->getData()->componentClass, $this->getData()->componentId, 'default'
        );
        Vps_Media::getOutputCache()->remove($cacheId);
    }
}
