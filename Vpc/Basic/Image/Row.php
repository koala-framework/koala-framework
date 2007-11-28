<?php
class Vpc_Basic_Image_Row extends Vps_Db_Table_Row implements Vpc_FileInterface
{
    private $_deleteFileRow;

    public function getImageUrl($type = 'default', $addRandom = false)
    {
        $id = $this->page_id . $this->component_key;
        $filename = $this->filename != '' ? $this->filename : 'unnamed';
        $row = $this->findParentRow('Vps_Dao_File');
        if (!$row) return null;
        return $row->generateUrl($this->getTable()->getComponentClass(), $id,
                                 $filename, $type, $addRandom);
    }

    private function _getScaleSettings()
    {
        $ret['scale'] = Vpc_Abstract::getSetting(
                                $this->getTable()->getComponentClass(), 'scale');
        if (is_array($ret['scale'])) {
            if (count($ret['scale']) == 1 && isset($ret['scale'][0])) {
                $ret['scale'] = $ret['scale'][0];
            } else {
                $ret['scale'] = $this->scale;
            }
        }
        $dimension = Vpc_Abstract::getSetting(
                            $this->getTable()->getComponentClass(), 'dimension');
        if (isset($dimension[0]) && !is_array($dimension[0])) {
            $ret['width'] = $dimension[0];
            $ret['height'] = $dimension[1];
        } else {
            $ret['width'] = $this->width;
            $ret['height'] = $this->height;
        }
        return $ret;
    }
    public function createCacheFile($source, $target, $type)
    {
        if ($type == 'default') {
            $s = $this->_getScaleSettings();
            Vps_Media_Image::scale($source, $target,
                                array($s['width'], $s['height']), $s['scale']);
        }
        $outputDimensions = Vpc_Abstract::getSetting(
                                    $this->getTable()->getComponentClass(),
                                    'ouputDimensions');
        if (isset($outputDimensions[$type])) {
            $s = $outputDimensions[$type];
            if (!isset($s[2])) $s[2] = Vps_Media_Image::SCALE_BESTFIT;
            Vps_Media_Image::scale($source, $target, array($s[0], $s[1]), $s[2]);
        } else {
            throw new Vps_Exception("Undefined outputDimension: '$type'");
        }
    }

    public function getImageDimension()
    {
        $s = $this->_getScaleSettings();
        $fileRow = $this->findParentRow('Vps_Dao_File');
        if ($fileRow) {
            return Vps_Media_Image::calculateScaleDimensions(
                $fileRow->getFileSource(),
                array($s['width'], $s['height']), $s['scale']);
        } else {
            return array('width' => 0, 'height' => 0);
        }
    }

    protected function _delete()
    {
        $this->_deleteFileRow = $this->findParentRow('Vps_Dao_File');
    }

    protected function _postDelete()
    {
        if ($this->_deleteFileRow) {
            $this->_deleteFileRow->delete();
        }
    }
}
