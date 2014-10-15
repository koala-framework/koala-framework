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
        $ret = null;
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
                        $ret = new Kwf_Assets_Dependency_Dependencies(array(
                            new Kwf_Assets_Dependency_File_Js($type.'/'.$f),
                            new Kwf_Assets_Dependency_File_Css($type.'/'.substr($f, 0, -2).'css'),
                        ), $dependencyName);
                        break;
                    } else {
                        $ret = new Kwf_Assets_Dependency_File_Js($type.'/'.$f);
                        break;
                    }
                }
            }
            if (!$ret) throw new Kwf_Exception("Can't find built dependency for $dependencyName in vendor/bower_components/$this->_path");
            if (file_exists($dir.'/bower.json')) {
                $bower = json_decode(file_get_contents($dir.'/bower.json'), true);
                if (isset($bower['dependencies'])) {
                    foreach ($bower['dependencies'] as $depName=>$version) {
                        $d = $this->_providerList->findDependency($depName);
                        if (!$d) {
                            throw new Kwf_Exception("Can't find dependency '$depName'");
                        }
                        $ret->addDependency(Kwf_Assets_Dependency_Abstract::DEPENDENCY_TYPE_REQUIRES, $d);
                    }
                }
            }
        } else if (substr($dependencyName, 0, 8) == 'FontFace' && strlen($dependencyName) > 8) {
            //eg. FontFaceIcomoon, FontFaceFontAwesome, FontFaceIonicons
            $bowerName = substr($dependencyName, 8);
            $bowerName[0] = strtolower($bowerName[0]);

            if (file_exists('vendor/bower_components/'.$bowerName.'/fonts.css')) {
                $ret = new Kwf_Assets_Dependency_File_Css($bowerName.'/fonts.css');
            } else if (file_exists('vendor/bower_components/'.$bowerName.'-font/fonts.css')) {
                $ret = new Kwf_Assets_Dependency_File_Css($bowerName.'-font/fonts.css');
            } else if (file_exists('vendor/bower_components/'.$bowerName.'-fonts/fonts.css')) {
                $ret = new Kwf_Assets_Dependency_File_Css($bowerName.'-fonts/fonts.css');

            } else if (file_exists('vendor/bower_components/'.$bowerName)) {
                $ret = new Kwf_Assets_Dependency_FontFace($bowerName, $bowerName);
            } else if (file_exists('vendor/bower_components/'.$bowerName.'-font')) {
                $ret = new Kwf_Assets_Dependency_FontFace($bowerName, $bowerName.'-font');
            } else if (file_exists('vendor/bower_components/'.$bowerName.'-fonts')) {
                $ret = new Kwf_Assets_Dependency_FontFace($bowerName, $bowerName.'-fonts');
            } else {
                throw new Kwf_Exception("Can't find font dependency for $dependencyName");
            }
        }
        return $ret;
    }
}
