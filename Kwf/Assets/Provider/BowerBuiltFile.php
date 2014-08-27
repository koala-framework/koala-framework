<?php
class Kwf_Assets_Provider_BowerBuiltFile extends Kwf_Assets_Provider_Abstract
{
    private $_path;
    public function __construct($path)
    {
        $path = substr($path, strlen(VENDOR_PATH.'/bower_components/'));
        $this->_path = $path;
    }

    public function getDependency($dependencyName)
    {
        if (strtolower($dependencyName) == $this->_path || strtolower($dependencyName).'.js' == $this->_path) {
            $dir = VENDOR_PATH.'/bower_components/'.$this->_path;
            $path = $this->_path;
            if (substr($path, -3) == '.js') $path = substr($path, 0, -3);
            $type = $path;
            if (substr($type, -2) == 'js') $type = substr($type, 0, -2);
            if (substr($path, -3) == '-js') $path = substr($path, 0, -3);
            $files = array(
                $path.'.js',
                'dist/'.$path.'.js',
                'build/'.$path.'.js',
                'src/'.$path.'.js',
                'js/'.$path.'.js',
                $path.'/'.$path.'.js',
            );
            foreach ($files as $f) {
                if (substr($f, -6) == '.js.js') $f = substr($f, 0, -3);
                if (file_exists($dir.'/'.$f)) {
                    if (file_exists($dir.'/'.substr($f, 0, -2).'css')) {
                        return new Kwf_Assets_Dependency_Dependencies(array(
                            new Kwf_Assets_Dependency_File_Js($type.'/'.$f),
                            new Kwf_Assets_Dependency_File_Css($type.'/'.substr($f, 0, -2).'css'),
                        ), $dependencyName);
                    } else {
                        return new Kwf_Assets_Dependency_File_Js($type.'/'.$f);
                    }
                }
            }
            throw new Kwf_Exception("Can't find built dependency for $dependencyName in vendor/bower_components/$this->_path");
        } else if (substr($dependencyName, 0, 8) == 'FontFace' && strlen($dependencyName) > 8) {
            //eg. FontFaceIcomoon, FontFaceFontAwesome, FontFaceIonicons
            $bowerName = substr($dependencyName, 8);
            $bowerName[0] = strtolower($bowerName[0]);

            if (file_exists('vendor/bower_components/'.$bowerName.'/fonts.css')) {
                return new Kwf_Assets_Dependency_File_Css($bowerName.'/fonts.css');
            } else if (file_exists('vendor/bower_components/'.$bowerName.'-font/fonts.css')) {
                return new Kwf_Assets_Dependency_File_Css($bowerName.'-font/fonts.css');
            } else if (file_exists('vendor/bower_components/'.$bowerName.'-fonts/fonts.css')) {
                return new Kwf_Assets_Dependency_File_Css($bowerName.'-fonts/fonts.css');

            } else if (file_exists('vendor/bower_components/'.$bowerName)) {
                return new Kwf_Assets_Dependency_FontFace($bowerName, $bowerName);
            } else if (file_exists('vendor/bower_components/'.$bowerName.'-font')) {
                return new Kwf_Assets_Dependency_FontFace($bowerName, $bowerName.'-font');
            } else if (file_exists('vendor/bower_components/'.$bowerName.'-fonts')) {
                return new Kwf_Assets_Dependency_FontFace($bowerName, $bowerName.'-fonts');
            }
        }
        return null;
    }
}
