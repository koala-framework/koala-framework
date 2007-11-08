<?php
class Vpc_Basic_Image_Row extends Vps_Db_Table_Row implements Vpc_FileInterface
{
    const DIMENSION_NORMAL = 'normal';
    const DIMENSION_THUMB = 'thumb';
    const DIMENSION_MINI = 'mini';
    
    public function getImageUrl($class, $dimension)
    {
        $id = $this->page_id . $this->component_key;
        $filename = $this->filename != '' ? $this->filename : 'unnamed';
        $random = uniqid();
        return "/media/$class/$id/$dimension?$random";
    }

    public function createCacheFile($source, $target)
    {
        Vps_Media_Image::scale($source, $target, array($this->width, $this->height), $this->scale);
        if (strpos($target, self::DIMENSION_THUMB)) {
            Vps_Media_Image::scale($target, $target, array(100, 100), Vps_Media_Image::SCALE_BESTFIT);
        } else if (strpos($target, self::DIMENSION_MINI)) {
            Vps_Media_Image::scale($target, $target, array(20, 20), Vps_Media_Image::SCALE_BESTFIT);
        }
    }
}
