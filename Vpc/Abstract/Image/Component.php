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
        }
    }

    public function getTemplateVars()
    {
        $ret = parent::getTemplateVars();
        $ret['row'] = $this->_getRow();
        $ret['imageRow'] = $this->getImageRow();
        return $ret;
    }

    public function hasContent()
    {
        $imageRow = $this->getImageRow();
        if ($imageRow && $imageRow->imageExists()) {
            return true;
        }
        return false;
    }

    public function getImageUrl()
    {
        $row = $this->getImageRow();
        $fRow = false;
        if ($row) $fRow = $row->getParentRow('Image');
        if ($fRow) {
            $filename = $row->filename;
            if (!$filename) {
                $filename = $fRow->filename;
            }
            $filename .= '.'.$fRow->extension;
            $id = $this->getData()->componentId;
            return Vps_Media::getUrl(get_class($this), $id, 'default', $filename);
        }
        return null;
    }

    public function getImageRow()
    {
        return $this->_getRow();
    }

    public function _getCacheRow()
    {
        return $this->getImageRow();
    }

    protected static function _getEmptyImage($className)
    {
        return false;
    }

    public function getImageDimensions()
    {
        $row = $this->getImageRow();
        $dimension = $this->_getSetting('dimensions');

        $s = array();
        if (sizeof($dimension) > 1 && isset($dimension[$row->dimension])) {
            $d = $dimension[$row->dimension];
        } else {
            reset($dimension);
            $d = current($dimension);
        }

        if (!isset($d['width'])) {
            $s['width'] = 0;
        } else if ($d['width'] == self::USER_SELECT) {
            $s['width'] = $row->width;
        } else {
            $s['width'] = $d['width'];
        }
        if (!isset($d['height'])) {
            $s['height'] = 0;
        } else if ($d['height'] == self::USER_SELECT) {
            $s['height'] = $row->height;
        } else {
            $s['height'] = $d['height'];
        }
        if (!isset($d['scale'])) {
        } else if ($d['scale'] == self::USER_SELECT) {
            $s['scale'] = $row->scale;
        } else {
            $s['scale'] = $d['scale'];
        }

        if (!$row || !$fileRow = $row->getParentRow('Image')) {
            $file = call_user_func(array($className, '_getEmptyImage'), $className);
        } else {
            $file = $fileRow->getFileSource();
        }
        if ($file && file_exists($file)) {
            $sourceSize = @getimagesize($file);
            if (!$sourceSize) return null;
            return Vps_Media_Image::calculateScaleDimensions($sourceSize, $s);
        }
        return $s;
    }

    public static function getMediaOutput($id, $type, $className)
    {
        $component = Vps_Component_Data_Root::getInstance()->getComponentById($id, array('ignoreVisible' => true));
        if (!$component) return null;

        $row = $component->getComponent()->getImageRow();
        if ($row) {
            $fileRow = $row->getParentRow('Image');
        } else {
            $fileRow = false;
        }
        if (!$fileRow) {
            $file = call_user_func(array($className, '_getEmptyImage'), $className);
            $s = getimagesize($file);
            $mimeType = $s['mime'];
        } else {
            $file = $fileRow->getFileSource();
            $mimeType = $fileRow->mime_type;
        }
        if (!$file || !file_exists($file)) {
            return null;
        }

        $dim = $component->getComponent()->getImageDimensions();
        if ($dim) {
            $output = Vps_Media_Image::scale($file, $dim);
        } else {
            $output = file_get_contents($file);
        }
        $ret = array(
            'contents' => $output,
            'mimeType' => $mimeType,
        );
        if (Vps_Registry::get('config')->debug->componentCache->checkComponentModification) {
            $mtimeFiles = array();
            $mtimeFiles[] = $file;
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
            $ret['mtime'] = filemtime($file);
        }
        if ($row) {
            Vps_Component_Cache::getInstance()->saveMeta(
                get_class($row->getModel()), $row->component_id, $id, Vps_Component_Cache::META_CALLBACK
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
