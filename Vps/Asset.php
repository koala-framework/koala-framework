<?php
class Vps_Asset
{
    private $_icon;
    private $_type;
    public function __construct($icon, $type = null)
    {
        if (!$type) {
            $paths = Zend_Registry::get('config')->path;
            if (file_exists($paths->vps.'/images/'.$icon.'.png')) {
                $filename = $paths->vps.'/images/'.$icon.'.png';
                $type = 'vps/images';
                $icon .= '.png';
            } else if (file_exists($paths->vps.'/images/'.$icon.'.gif')) {
                $filename = $paths->vps.'/images/'.$icon.'.gif';
                $type = 'vps/images';
                $icon .= '.gif';
            } else if (file_exists($paths->vps.'/images/'.$icon.'.jpg')) {
                $filename = $paths->vps.'/images/'.$icon.'.jpg';
                $type = 'vps/images';
                $icon .= '.jpg';
            } else if (file_exists($paths->silkicons.'/'.$icon)) {
                $filename = $paths->silkicons.'/'.$icon;
                $type = 'silkicons';
            } else if (file_exists($paths->silkicons.'/'.$icon.'.png')) {
                $filename = $paths->silkicons.'/'.$icon.'.png';
                $type = 'silkicons';
                $icon .= '.png';
            } else {
                throw new Vps_Exception("Asset '$icon' not found");
            }
        }
        $this->_type = $type;
        $this->_icon = $icon;
    }
    
    public function getFilename()
    {
        $paths = Zend_Registry::get('config')->path;
        return $paths->{$this->_type}.'/'.$this->_icon;
    }

    public function __toString()
    {
        return '/assets/'.$this->_type.'/'.$this->_icon;
    }
}
