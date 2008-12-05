<?php
class Vpc_Basic_Image_Component extends Vpc_Abstract
    implements Vps_Media_Output_Interface
{
    public static function getSettings()
    {
        $ret = array_merge(parent::getSettings(), array(
            'componentName'     => trlVps('Image'),
            'componentIcon'     => new Vps_Asset('picture'),
            'modelname'         => 'Vpc_Basic_Image_Model',

            'dimensions'        => array(300, 200, Vps_Media_Image::SCALE_BESTFIT), // Leeres Array -> freie Wahl, array(width, height), array(array(width, height), null: Bild in Originalgröße)
            'editComment'       => false,
            'editFilename'      => false,
            'allowBlank'        => true,
            'pdfMaxWidth'       => 0,
            'pdfMaxDpi'         => 150,
            'imgCssClass'       => '',
            'emptyImage'        => false,
            'useParentImage'    => false,
            'showHelpText'      => false
        ));
        $ret['assetsAdmin']['dep'][] = 'VpsSwfUpload';
        return $ret;
    }

    public function getTemplateVars()
    {
        $ret = parent::getTemplateVars();
        $ret['row'] = $this->getImageRow();
        if (Vpc_Abstract::getSetting(get_class($this), 'editComment')) {
            $ret['comment'] = $ret['row']->comment;
        }
        $ret['imgCssClass'] = $this->_getSetting('imgCssClass');
        return $ret;
    }

    public function hasContent()
    {
        if ($this->_getSetting('useParentImage')) {
            return $this->getData()->parent->hasContent();
        }
        $imageRow = $this->getImageRow();
        if ($imageRow && $imageRow->vps_upload_id) {
            return true;
        }
        if ($this->_getSetting('emptyImage')) return true;
        return false;
    }

    public function getImageUrl()
    {
        $row = $this->getImageRow();
        $fRow = false;
        if ($row) $fRow = $row->getParentRow('Image');
        if (!$fRow) {
            $file = self::_getEmptyImage(get_class($this));
            if (!$file) return null;
            $filename = $this->_getSetting('emptyImage');
            $id = $this->getData()->dbId;
        } else {
            $filename = $row->filename;
            if (!$filename) {
                $filename = $fRow->filename;
            }
            $filename .= '.'.$fRow->extension;
            $id = $row->component_id;
        }
        return Vps_Media::getUrl(get_class($this), $id, 'default', $filename);
    }

    public function getImageDimensions()
    {
        return self::_getDimensions($this->getImageRow(), get_class($this));
    }

    public function getImageRow()
    {
        if ($this->_getSetting('useParentImage')) {
            return $this->getModel()->getRow($this->getData()->parent->dbId);
        } else {
            return $this->_getRow();
        }
    }

    private static function _getDimensions($row, $className)
    {
        $dimension = Vpc_Abstract::getSetting($className, 'dimensions');

        $s = array();
        if (isset($dimension[0]) && !is_array($dimension[0])) {
            $s['width'] = $dimension[0];
            $s['height'] = $dimension[1];
            $s['scale'] = isset($dimension[2]) ? $dimension[2] : false;
        } else { // aus DB
            $s['width'] = $row->width;
            $s['height'] = $row->height;
            $s['scale'] = $row->scale;
        }
        if (!$row || !$fileRow = $row->getParentRow('Image')) {
            $file = self::_getEmptyImage($className);
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

    private static function _getEmptyImage($className)
    {
        $emptyImage = Vpc_Abstract::getSetting($className, 'emptyImage');
        if (!$emptyImage) return null;
        $ext = substr($emptyImage, strrpos($emptyImage, '.') + 1);
        $filename = substr($emptyImage, 0, strrpos($emptyImage, '.'));
        return Vpc_Admin::getComponentFile($className, $filename, $ext);
    }

    public static function getMediaOutput($id, $type, $className)
    {
        $row = Vpc_Abstract::createModel($className)->getRow($id);
        if ($row) {
            $fileRow = $row->getParentRow('Image');
        } else {
            $fileRow = false;
        }
        if (!$fileRow) {
            $file = self::_getEmptyImage($className);
            $s = getimagesize($file);
            $mimeType = $s['mime'];
        } else {
            $file = $fileRow->getFileSource();
            $mimeType = $fileRow->mime_type;
        }
        if (!$file || !file_exists($file)) {
            return null;
        }

        $output = Vps_Media_Image::scale($file, self::_getDimensions($row, $className));
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
        return $ret;
    }
}
