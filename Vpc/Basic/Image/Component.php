<?php
class Vpc_Basic_Image_Component extends Vpc_Abstract implements Vpc_FileInterface
{
    protected $_tablename = 'Vpc_Basic_Image_Model';
    const NAME = 'Standard.Image';
    protected $_settings = array(
        'extensions' 	    => array('jpg', 'gif', 'png'),
        'dimension'         => array(300, 200), // Leeres Array -> freie Wahl, array(width, height), array(array(width, height), ...)
        'allow'             => array(Vps_Media_Image::SCALE_BESTFIT),
        'filename'          => 'filename',
        'editFilename'      => true,
        'allowBlank'        => true,
        'hasEnlarge'        => true,
        'enlargeClass'      => 'Vpc_Basic_Image_Component',
        'enlargeSettings'   => array(
            'extensions'        => array('jpg', 'gif', 'png'),
            'dimension'         => array(800, 600), // Leeres Array -> freie Wahl, array(width, height), array(array(width, height), ...)
            'allow'             => array(Vps_Media_Image::SCALE_BESTFIT),
            'filename'          => 'filename',
            'editFilename'      => true
        )
    );
    const DIMENSION_NORMAL = '';
    const DIMENSION_THUMB = '.thumb';
    const DIMENSION_MINI = '.mini';
    public $imagebig = null;

    protected function _init()
    {
        if (!$this->imagebig && $this->getSetting('hasEnlarge')) {
            $enlargeClass = $this->_getClassFromSetting('enlargeClass', 'Vpc_Basic_Image_Component');
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
        $s = $this->getImageDimension();
        $return['width'] = $s['width'];
        $return['height'] = $s['height'];

        if ($this->getSetting('hasEnlarge') && $this->imagebig) {
            $return['enlargeUrl'] = $this->imagebig->getImageUrl();
        }

        $return['template'] = 'Basic/Image.html';
        return $return;
    }

    public function getSettings()
    {
        $settings = parent::getSettings();
        if ((!isset($settings['width']) || $settings['width'] == '') && isset($settings['dimension'][0])) {
            if (is_array($settings['dimension'][0])) {
                $settings['width'] = $settings['dimension'][0][0];
                $settings['height'] = $settings['dimension'][0][1];
            } else {
                $settings['width'] = $settings['dimension'][0];
                $settings['height'] = $settings['dimension'][1];
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