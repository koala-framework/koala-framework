<?php
class Vpc_Basic_Image_Index extends Vpc_Abstract implements Vpc_FileInterface
{
    protected $_tablename = 'Vpc_Basic_Image_IndexModel';
    const NAME = 'Standard.Image';
    protected $_settings = array(
        'extensions' 	    => array('jpg', 'gif', 'png'),
        'size'              => array(300, 200), // Leeres Array -> freie Wahl, array(width, height), array(array(width, height), ...)
        'allow'             => array(Vps_Media_Image::SCALE_BESTFIT),
        'filename'          => 'filename',
        'editFilename'      => true,
        'allowBlank'        => true,
        'hasEnlarge'        => true,
        'enlargeClass'      => 'Vpc_Basic_Image_Index',
        'enlargeSettings'   => array(
            'extensions'        => array('jpg', 'gif', 'png'),
            'size'              => array(800, 600), // Leeres Array -> freie Wahl, array(width, height), array(array(width, height), ...)
            'allow'             => array(Vps_Media_Image::SCALE_BESTFIT),
            'filename'          => 'filename',
            'editFilename'      => true
        )
    );
    const SIZE_NORMAL = '';
    const SIZE_THUMB = '.thumb';
    const SIZE_MINI = '.mini';
    public $imagebig = null;

    protected function _init()
    {
        if (!$this->imagebig && $this->getSetting('hasEnlarge')) {
            $enlargeClass = $this->_getClassFromSetting('enlargeClass', 'Vpc_Basic_Image_Index');
            $settings = $this->getSetting('enlargeSettings');
            if (!is_array($settings)) { $settings = array(); }
            $settings['hasEnlarge'] = false;
            $this->imagebig = $this->createComponent($enlargeClass, 1, $settings);
        }
    }

    public function getTemplateVars()
    {
        $return = parent::getTemplateVars();
        $return['url'] = $this->getImageUrl();
        $return['template'] = 'Basic/Image.html';
        return $return;
    }

    public function getSettings()
    {
        $settings = parent::getSettings();
        if ((!isset($settings['width']) || $settings['width'] == '') && isset($settings['size'][0])) {
            if (is_array($settings['size'][0])) {
                $settings['width'] = $settings['size'][0][0];
                $settings['height'] = $settings['size'][0][1];
            } else {
                $settings['width'] = $settings['size'][0];
                $settings['height'] = $settings['size'][1];
            }
        }
        if (!isset($settings['scale']) || $settings['scale'] == '') {
            $settings['scale'] = isset($settings['allow'][0]) ?
                            $settings['allow'][0] : Vps_Media_Image::SCALE_BESTFIT;
        }
        return $settings;
    }

    public function getChildComponents()
    {
        return $this->imagebig ? array($this->imagebig) : array();
    }

    public function getImageUrl($size = self::SIZE_NORMAL, $addRandom = false)
    {
        $row = $this->getTable()->find($this->getDbId(),
                                        $this->getComponentKey())->current();
        if ($row) {
            $filename = $row->filename != '' ? $row->filename : 'unnamed';
            $filename .= $size;
            return $this->getTable('Vps_Dao_File')
                    ->generateUrl($row->vps_upload_id, $this->getId(), $filename,
                                  Vps_Dao_File::SHOW, $addRandom);
        } else {
            return null;
        }
    }

    public function createCacheFile($source, $target)
    {
        $sizes = $this->getSetting('size');
        if (sizeof($sizes) == 2 && !is_array($sizes[0])) {
            $width = $sizes[0];
            $height = $sizes[1];
        } else {
            $width = $this->getSetting('width');
            $height = $this->getSetting('height');
        }
        $scale = $this->getSetting('scale');
        Vps_Media_Image::scale($source, $target, array($width, $height), $scale);

        if (strpos($target, self::SIZE_THUMB)) {
            Vps_Media_Image::scale($target, $target, array(100, 100), Vps_Media_Image::SCALE_BESTFIT);
        } else if (strpos($target, self::SIZE_MINI)) {
            Vps_Media_Image::scale($target, $target, array(20, 20), Vps_Media_Image::SCALE_BESTFIT);
        }
    }
}