<?php
class Vpc_Basic_Image_Index extends Vpc_Abstract implements Vpc_FileInterface
{
    protected $_tablename = 'Vpc_Basic_Image_IndexModel';
    const NAME = 'Standard.Image';
    protected $_settings = array(
        'extensions' 	    => array('jpg', 'gif', 'png'),
        'size'              => array(), // Leeres Array -> freie Wahl, array(width, height), array(array(width, height), ...)
        'allow'		        => array('crop', 'scale', 'bestfit') //keywords: crop, scale, bestfit
    );
    const SIZE_NORMAL = '';
    const SIZE_THUMB = '.thumb';
    const SIZE_ORIGINAL = '.original';

    public function getTemplateVars()
    {
        $return = parent::getTemplateVars();
        $return['url'] = $this->getImageUrl();
        $return['template'] = 'Basic/Image.html';
        return $return;
    }

    public function getImageUrl($size = self::SIZE_NORMAL)
    {
        $row = $this->_getTable()->find($this->getDbId(), $this->getComponentKey())->current();
        if ($row) {
            $filename = $row->name != '' ? $row->name : 'unnamed';
            $filename .= $size;
            return $this->_getTable('Vps_Dao_File')->generateUrl($row->vps_upload_id, $this->getId(), $filename);
        } else {
            return null;
        }
    }

    public function createCacheFile($source, $target)
    {
        if (strpos($target, self::SIZE_THUMB)) {
            $this->setSetting('width', 100);
            $this->setSetting('height', 100);
        }

        $width = $this->getSetting('width');
        $height = $this->getSetting('height');
        $style = $this->getSetting('style');
        if ($width <= 0) { $width = 100; }
        if ($height <= 0) { $height = 100; }

        Vps_Media_Image::scale($source, $target, array($width, $height), $style);
    }
}