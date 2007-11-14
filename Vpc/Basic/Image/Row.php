<?php
class Vpc_Basic_Image_Row extends Vps_Db_Table_Row implements Vpc_FileInterface
{
    const DIMENSION_NORMAL = 'normal';
    const DIMENSION_THUMB = 'thumb';
    const DIMENSION_MINI = 'mini';

    private $_deleteFileRow;

    public function getImageUrl($class, $dimension = self::DIMENSION_NORMAL, $addRandom = false)
    {
        $id = $this->page_id . $this->component_key;
        $filename = $this->filename != '' ? $this->filename : 'unnamed';
        if ($dimension != self::DIMENSION_NORMAL) {
            $filename .= '.'.$dimension;
        }
        $row = $this->findParentRow('Vps_Dao_File');
        if (!$row) return null;
        return $row->generateUrl($class, $id, $filename, Vps_Dao_Row_File::SHOW, $addRandom);
    }

    private function _getScaleSettings($class)
    {
        $ret['scale'] = Vpc_Abstract::getSetting($class, 'scale');
        if (is_array($ret['scale'])) {
            if (count($ret['scale']) == 1 && isset($ret['scale'][0])) {
                $ret['scale'] = $ret['scale'][0];
            } else {
                $ret['scale'] = $this->scale;
            }
        }
        $dimension = Vpc_Abstract::getSetting($class, 'dimension');
        if (isset($dimension[0]) && !is_array($dimension[0])) {
            $ret['width'] = $dimension[0];
            $ret['height'] = $dimension[1];
        } else {
            $ret['width'] = $this->width;
            $ret['height'] = $this->height;
        }
        return $ret;
    }
    public function createCacheFile($class, $source, $target)
    {
        $s = $this->_getScaleSettings($class);
        Vps_Media_Image::scale($source, $target, array($s['width'], $s['height']), $s['scale']);
        if (strpos($target, self::DIMENSION_THUMB)) {
            Vps_Media_Image::scale($target, $target, array(100, 100), Vps_Media_Image::SCALE_BESTFIT);
        } else if (strpos($target, self::DIMENSION_MINI)) {
            Vps_Media_Image::scale($target, $target, array(20, 20), Vps_Media_Image::SCALE_BESTFIT);
        }
    }

    public function getImageDimension($class)
    {
        $s = $this->_getScaleSettings($class);
        if ($this->vps_upload_id) {
            return Vps_Media_Image::calculateScaleDimensions(
                $this->findParentRow('Vps_Dao_File')->getFileSource(),
                array($s['width'], $s['height']), $s['scale']
            );
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
