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
            if (!isset($paths)) {
                $cacheId = 'asset-paths';
                $paths = Kwf_Cache_SimpleStatic::fetch($cacheId);
                if ($paths === false) {
                    $paths = array(
                        'web' => '.'
                    );
                    $vendors = glob(VENDOR_PATH."/*/*");
                    $vendors[] = KWF_PATH; //required for kwf tests, in web kwf is twice in $vendors but that's not a problem
                    foreach ($vendors as $i) {
                        if (is_dir($i) && file_exists($i.'/dependencies.ini')) {
                            $dep = new Zend_Config_Ini($i.'/dependencies.ini', 'config');
                            $paths[$dep->pathType] = $i;
                        }
                    }
                    Kwf_Cache_SimpleStatic::add($cacheId, $paths);
                }
            }
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
        $file = new Kwf_Assets_Dependency_File(Kwf_Assets_ProviderList_Default::getInstance(), $d['type'].'/'.$d['icon']);
        return $file->getAbsoluteFileName();
    }

    public function toString($effects = array())
    {
        $d = $this->_getIconAndType();
        if ($effects) {
            $str = 'fx';
            foreach ($effects as $effect) $str .= '_' . $effect;
            return '/assets/'.$str.'/'.$d['type'].'/'.$d['icon'];
        } else {
            return '/assets/'.$d['type'].'/'.$d['icon'];
        }
    }

    public function __toString()
    {
        return $this->toString();
    }
}
