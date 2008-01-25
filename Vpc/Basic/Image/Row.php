<?php
class Vpc_Basic_Image_Row extends Vps_Db_Table_Row
{
    private $_deleteFileRow;

    private function _getScaleSettings()
    {
        $ret['scale'] = Vpc_Abstract::getSetting($this->getTable()->getComponentClass(), 'scale');
        if (is_array($ret['scale'])) {
            if (count($ret['scale']) == 1 && isset($ret['scale'][0])) {
                $ret['scale'] = $ret['scale'][0];
            } else {
                $ret['scale'] = $this->scale;
            }
        }
        $dimension = Vpc_Abstract::getSetting($this->getTable()->getComponentClass(), 'dimension');
        if (isset($dimension[0]) && !is_array($dimension[0])) {
            $ret['width'] = $dimension[0];
            $ret['height'] = $dimension[1];
        } else {
            $ret['width'] = $this->width;
            $ret['height'] = $this->height;
        }
        return $ret;
    }

    protected function _createCacheFile($source, $target, $type)
    {
        if ($type == 'default') {
            $s = $this->_getScaleSettings();
            Vps_Media_Image::scale(
                $source, $target,
                array($s['width'], $s['height']), $s['scale']
            );
        } else {
            $outputDimensions = Vpc_Abstract::getSetting($this->getTable()->getComponentClass(),
                                        'ouputDimensions');
            if (isset($outputDimensions[$type])) {
                $s = $outputDimensions[$type];
                if (!isset($s[2])) $s[2] = Vps_Media_Image::SCALE_BESTFIT;
                Vps_Media_Image::scale($source, $target, array($s[0], $s[1]), $s[2]);
            } else {
                parent::_createCacheFile($source, $target, $type);
            }
        }
    }

    public function getFileUrl($rule = null, $type = 'default', $filename = null, $addRandom = false)
    {
        if ($this->filename != '') {
            $filename = $this->filename;
        }
        return parent::getFileUrl($rule, $type, $filename, $addRandom);
    }

    public function getImageDimension()
    {
        $s = $this->_getScaleSettings();
        $fileRow = $this->findParentRow('Vps_Dao_File');
        if ($fileRow) {
            return Vps_Media_Image::calculateScaleDimensions($fileRow->getFileSource(),
                array($s['width'], $s['height']), $s['scale']);
        } else {
            return array('width' => 0, 'height' => 0);
        }
    }
}
