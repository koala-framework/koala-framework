<?php
class Vpc_Basic_Image_Enlarge_Component extends Vpc_Basic_Image_Component
{
    const DIMENSION_NORMAL = '';
    const DIMENSION_THUMB = '.thumb';
    const DIMENSION_MINI = '.mini';
    public $enlargeImage = null;

    public static function getSettings()
    {
        return array_merge(parent::getSettings(), array(
            'enlargeClass'      => 'Vpc_Basic_Image_Component',
        ));
    }
    
    protected function _init()
    {
        if (!$this->enlargeImage) {
            $enlargeClass = $this->_getClassFromSetting('enlargeClass', 'Vpc_Basic_Image_Component');
            $this->enlargeImage = $this->createComponent($enlargeClass, 1);
        }
    }

    public function getTemplateVars()
    {
        $return = parent::getTemplateVars();
        $return['enlargeImage'] = $this->enlargeImage->getTemplateVars();
        $return['template'] = 'Basic/EnlargeImage.html';
        return $return;
    }

    public function getChildComponents()
    {
        return $this->imagebig ? array($this->imagebig) : array();
    }

    public function getImageUrl($dimension = self::DIMENSION_NORMAL, $addRandom = false)
    {
        $row = $this->getTable()->find($this->getDbId(),
                                        $this->getComponentKey())->current();

        if ($row) {
            $filename = $row->filename != '' ? $row->filename : 'unnamed';
            $filename .= $dimension;
            return $this->getTable('Vps_Dao_File')
                    ->generateUrl($row->vps_upload_id, $this->getId(), $filename,
                                  Vps_Dao_File::SHOW, $addRandom);
        } else {
            return null;
        }
    }

    public function getImageDimension()
    {
        $row = $this->getTable()->find($this->getDbId(),
                                        $this->getComponentKey())->current();
        if (!$row || !$row->vps_upload_id) return false;
        $scale = $this->getSetting('scale');
        return Vps_Media_Image::calculateScaleDimensions(
                    $this->getTable('Vps_Dao_File')->getFileSource($row->vps_upload_id),
                    $this->_getImageDimensionSetting(),
                    $scale);
    }

    private function _getImageDimensionSetting()
    {
        $dimensions = $this->getSetting('dimension');
        if (sizeof($dimensions) == 2 && !is_array($dimensions[0])) {
            $width = $dimensions[0];
            $height = $dimensions[1];
        } else {
            $width = $this->getSetting('width');
            $height = $this->getSetting('height');
        }
        return array($width, $height);
    }

    public function createCacheFile($source, $target)
    {
        $scale = $this->getSetting('scale');
        Vps_Media_Image::scale($source, $target, $this->_getImageDimensionSetting(), $scale);

        if (strpos($target, self::DIMENSION_THUMB)) {
            Vps_Media_Image::scale($target, $target, array(100, 100), Vps_Media_Image::SCALE_BESTFIT);
        } else if (strpos($target, self::DIMENSION_MINI)) {
            Vps_Media_Image::scale($target, $target, array(20, 20), Vps_Media_Image::SCALE_BESTFIT);
        }
    }
}