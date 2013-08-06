<?php
class Kwf_Asset
{
    private $_icon;
    private $_type;
    public function __construct($icon, $type = null)
    {
        $this->_icon = $icon;
        $this->_type = $type;
    }

    private function _getIconAndType()
    {
        $icon = $this->_icon;
        $type = $this->_type;
        if (!$type) {
            static $paths;
            if (!isset($paths)) $paths = Zend_Registry::get('config')->path->toArray();
            if (file_exists($paths['silkicons'].'/'.$icon)) {
                $filename = $paths['silkicons'].'/'.$icon;
                $type = 'silkicons';
            } else if (file_exists($paths['silkicons'].'/'.$icon.'.png')) {
                $filename = $paths['silkicons'].'/'.$icon.'.png';
                $type = 'silkicons';
                $icon .= '.png';
            } else if (file_exists($paths['kwf'].'/images/'.$icon.'.png')) {
                $filename = $paths['kwf'].'/images/'.$icon.'.png';
                $type = 'kwf/images';
                $icon .= '.png';
            } else if (file_exists($paths['kwf'].'/images/'.$icon.'.gif')) {
                $filename = $paths['kwf'].'/images/'.$icon.'.gif';
                $type = 'kwf/images';
                $icon .= '.gif';
            } else if (file_exists($paths['kwf'].'/images/'.$icon.'.jpg')) {
                $filename = $paths['kwf'].'/images/'.$icon.'.jpg';
                $type = 'kwf/images';
                $icon .= '.jpg';
            } else if (file_exists($paths['kwf'].'/images/fileicons/'.$icon.'.png')) {
                $filename = $paths['kwf'].'/images/fileicons/'.$icon.'.png';
                $type = 'kwf/images/fileicons';
                $icon .= '.png';
            } else if (file_exists($paths['kwf'].'/images/fileicons/'.$icon.'.jpg')) {
                $filename = $paths['kwf'].'/images/fileicons/'.$icon.'.jpg';
                $type = 'kwf/images/fileicons';
                $icon .= '.jpg';
            } else if (file_exists($paths['web'].'/images/icons/'.$icon)) {
                $filename = $paths['web'].'/images/icons/'.$icon;
                $type = 'web/images/icons/';
            } else if (file_exists($paths['web'].'/images/icons/'.$icon.'.png')) {
                $filename = $paths['web'].'/images/icons/'.$icon;
                $type = 'web/images/icons/';
                $icon .= '.png';
            } else {
                throw new Kwf_Exception("Asset '$icon' not found");
            }
        }
        return array(
            'type' => $type,
            'icon' => $icon
        );
    }

    public function getFilename()
    {
        $d = $this->_getIconAndType();
        static $paths;
        if (!isset($paths)) $paths = Zend_Registry::get('config')->path->toArray();
        return $paths[$d['type']].'/'.$d['icon'];
    }

    public function toString($effects = array())
    {
        $d = $this->_getIconAndType();
        if ($effects) {
            $str = 'fx';
            foreach ($effects as $effect) $str .= '_' . $effect;
            return Kwf_Setup::getBaseUrl().'/assets/'.$str.'/'.$d['type'].'/'.$d['icon'];
        } else {
            return Kwf_Setup::getBaseUrl().'/assets/'.$d['type'].'/'.$d['icon'];
        }
    }

    public function __toString()
    {
        return $this->toString();
    }
}
