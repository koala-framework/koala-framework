<?php
class Vpc_Basic_Image_Row extends Vpc_Row
{
    private function _getScaleSettings()
    {
        $dimension = Vpc_Abstract::getSetting($this->getTable()->getComponentClass(), 'dimensions');
        if (isset($dimension[0]) && !is_array($dimension[0])) {
            $ret['width'] = $dimension[0];
            $ret['height'] = $dimension[1];
            $ret['scale'] = $dimension[2];
        } else { // aus DB
            $ret['width'] = $this->width;
            $ret['height'] = $this->height;
            $ret['scale'] = $this->scale;
        }
        if (is_null($ret['width']) && is_null($ret['height'])) {
            $parentRow = $this->findParentRow('Vps_Dao_File');
            if ($parentRow) {
                $originalFile = $parentRow->getFileSource();
                if (is_file($originalFile)) {
                    $data = getimagesize($originalFile);
                    $ret['width'] = $data[0];
                    $ret['height'] = $data[1];
                }
            }
        }

        return $ret;
    }

    protected function _createCacheFile($source, $target, $type)
    {
        if ($type == 'default') {
            $s = $this->_getScaleSettings();
            Vps_Media_Image::scale($source, $target, $s);
        } else {
            $outputDimensions = Vpc_Abstract::getSetting($this->getTable()->getComponentClass(),
                                        'ouputDimensions');
            if (isset($outputDimensions[$type])) {
                $s = $outputDimensions[$type];
                Vps_Media_Image::scale($source, $target, $s);
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

    protected function _postUpdate()
    {
        parent::_postUpdate();
        $this->deleteFileCache();
    }
}
