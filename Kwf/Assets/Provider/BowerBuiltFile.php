<?php
class Kwf_Assets_Provider_BowerBuiltFile extends Kwf_Assets_Provider_Abstract
{
    private $_path;
    public function __construct($path)
    {
        $path = substr($path, strlen(VENDOR_PATH.'/bower_components/'));
        $this->_path = $path;
    }

    public function getDependencyNameByAlias($dependencyName)
    {
        if (strtolower($dependencyName) == strtolower($this->_path) || strtolower($dependencyName).'.js' == $this->_path) {
            return ucfirst($this->_path);
        }
    }

    private function _guessMainFiles($dependencyName)
    {
        $dir = VENDOR_PATH.'/bower_components/'.$this->_path;
        $path = $this->_path;
        if (substr($path, -3) == '.js') $path = substr($path, 0, -3);
        $type = $path;
        if (substr($type, -2) == 'js') $type = substr($type, 0, -2);
        if (substr($path, -3) == '-js') $path = substr($path, 0, -3);

        $paths = array($path);
        if (strpos($path, '-')!==false) $paths[] = str_replace('-', '.', $path);

        $files = array();
        foreach ($paths as $p) {
            $files = array_merge($files, array(
                array(
                    'file' => 'dist/'.$p.'.dist.js',
                    'additionalFiles' => array(
                        'dist/'.$p.'.dist.css',
                    )
                ),
                array(
                    'file' => $p.'.js',
                    'additionalFiles' => array(
                        $p.'.css',
                    )
                ),
                array(
                    'file' => 'jquery.'.$p.'.js',
                    'additionalFiles' => array(
                        'jquery.'.$p.'.css',
                    )
                ),
                array(
                    'file' => 'dist/'.$p.'.js',
                    'additionalFiles' => array(
                        'dist/'.$p.'.css',
                    )
                ),
                array(
                    'file' => 'dist/js/'.$p.'.js',
                    'additionalFiles' => array(
                        'dist/css/'.$p.'.css',
                    )
                ),
                array(
                    'file' => 'build/'.$p.'.js',
                    'additionalFiles' => array(
                        'build/'.$p.'.css',
                    )
                ),
                array(
                    'file' => 'src/'.$p.'.js',
                    'additionalFiles' => array(
                        'src/'.$p.'.css',
                    )
                ),
                array(
                    'file' => 'js/'.$p.'.js',
                    'additionalFiles' => array(
                        'js/'.$p.'.css',
                        'css/'.$p.'.css',
                    )
                ),
                array(
                    'file' => 'lib/'.$p.'.js',
                    'additionalFiles' => array(
                        'lib/'.$p.'.css',
                    )
                ),
                array(
                    'file' => $p.'/'.$p.'.js',
                    'additionalFiles' => array(
                        $p.'/'.$p.'.css',
                    )
                ),
                array(
                    'file' => 'bin/js/'.$p.'.js',
                    'additionalFiles' => array(
                        'bin/css/'.$p.'.css',
                    )
                )
            ));
        }
        foreach ($files as $f) {
            if (file_exists($dir.'/'.$f['file'])) {
                $baseFileName = substr($f['file'], 0, -3);
                if (file_exists($dir.'/'.$baseFileName.'.min.js') && file_exists($dir.'/'.$baseFileName.'.min.map')) {
                    //use shipped minimied+map file if exists
                    $jsDep = new Kwf_Assets_Dependency_File_JsPreBuilt($this->_providerList, $type.'/'.$f['file'], $type.'/'.$baseFileName.'.min.js', $type.'/'.$baseFileName.'.min.map');
                } else {
                    $jsDep = new Kwf_Assets_Dependency_File_Js($this->_providerList, $type.'/'.$f['file']);
                }
                $deps = array();
                foreach ($f['additionalFiles'] as $i) {
                    if (file_exists($dir.'/'.$i)) {
                        if (substr($i, -4) == '.css') {
                            $deps[] = new Kwf_Assets_Dependency_File_Css($this->_providerList, $type.'/'.$i);
                        } else if (substr($i, -3) == '.js') {
                            $deps[] = new Kwf_Assets_Dependency_File_Js($this->_providerList, $type.'/'.$i);
                        }
                    }
                }
                if ($deps) {
                    array_unshift($deps, $jsDep);
                    $ret = new Kwf_Assets_Dependency_Dependencies($this->_providerList, $deps, $dependencyName);
                    break;
                } else {
                    $ret = $jsDep;
                    break;
                }
            }
        }
        if (!$ret) throw new Kwf_Exception("Can't find built dependency for $dependencyName in vendor/bower_components/$this->_path");
        return $ret;
    }

    public function getDependency($dependencyName)
    {
        $ret = null;
        $matched = false;
        if (strtolower($dependencyName) == strtolower($this->_path)) {
            $matched = true;

        //some packages end with .js, strip that
        } else if (preg_match("#^".preg_quote($this->_path, '#').'\\.js$#i', $dependencyName)) {
            $matched = true;

        //also match if a prefix "foo-" is added in front of the package name
        //required to support npm names for bower packages (example: desandro-classie)
        } else if (preg_match("#^[a-z0-9]*-".preg_quote($this->_path, '#').'$#i', $dependencyName)) {
            $matched = true;
        }
        if ($matched) {
            $type = $this->_path;
            if (substr($type, -3) == '.js') {
                $type = substr($type, 0, -3);
            }
            $dir = VENDOR_PATH.'/bower_components/'.$this->_path;
            if (file_exists($dir.'/bower.json')) {
                $dependencies = array();
                $bower = json_decode(file_get_contents($dir.'/bower.json'), true);
                if (isset($bower['dependencies'])) {
                    foreach ($bower['dependencies'] as $depName=>$version) {
                        $d = $this->_providerList->findDependency($depName);
                        if (!$d) {
                            throw new Kwf_Exception("Can't find dependency '$depName'");
                        }
                        $dependencies[] = $d;
                    }
                }
                if (isset($bower['main'])) {
                    $main = $bower['main'];
                    if (is_string($main)) $main = array($main);
                    $mainDeps = array('js'=>array(), 'css'=>array());
                    foreach ($main as $mainFile) {
                        $mainFile = $mainFile;
                        if (substr($mainFile, -3) == '.js') {
                            if (substr($mainFile, -7) == '.min.js' || substr($mainFile, -7) == '-min.js') {
                                //we don't want to use min file
                                $mainFile = substr($mainFile, 0, -7).'.js';
                            }
                            if (file_exists($dir.'/'.substr($mainFile, 0, -3).'.min.js') && file_exists($dir.'/'.substr($mainFile, 0, -3).'.min.map')) {
                                //use shipped minimied+map file if exists
                                $d = new Kwf_Assets_Dependency_File_JsPreBuilt($this->_providerList, $type.'/'.$mainFile, $type.'/'.substr($mainFile, 0, -3).'.min.js', $type.'/'.substr($mainFile, 0, -3).'.min.map');
                            } elseif (file_exists($dir.'/'.substr($mainFile, 0, -3).'-min.js') && file_exists($dir.'/'.substr($mainFile, 0, -3).'-min.map')) {
                                //use shipped minimied+map file if exists
                                $d = new Kwf_Assets_Dependency_File_JsPreBuilt($this->_providerList, $type.'/'.$mainFile, $type.'/'.substr($mainFile, 0, -3).'-min.js', $type.'/'.substr($mainFile, 0, -3).'-min.map');
                            } else {
                                $d = new Kwf_Assets_Dependency_File_Js($this->_providerList, $type.'/'.$mainFile);
                            }
                            $mainDeps['js'][] = $d;
                        } elseif (substr($mainFile, -4) == '.css') {
                            $d = new Kwf_Assets_Dependency_File_Css($this->_providerList, $type.'/'.$mainFile);
                            $mainDeps['css'][] = $d;
                        }
                    }
                } else {
                    throw new Kwf_Exception("No main property in $dir/bower.json");
                }
                if (count($mainDeps['js']) == 1) {
                    //if there is a single js file in main return that and add the other dependencies
                    $ret = $mainDeps['js'][0];
                    $ret->addDependencies(Kwf_Assets_Dependency_Abstract::DEPENDENCY_TYPE_REQUIRES, $mainDeps['css']);
                    $ret->addDependencies(Kwf_Assets_Dependency_Abstract::DEPENDENCY_TYPE_REQUIRES, $dependencies);
                } else {
                    $ret = new Kwf_Assets_Dependency_Dependencies($this->_providerList, array(), $dependencyName);
                    $ret->addDependencies(Kwf_Assets_Dependency_Abstract::DEPENDENCY_TYPE_REQUIRES, $mainDeps['js']);
                    $ret->addDependencies(Kwf_Assets_Dependency_Abstract::DEPENDENCY_TYPE_REQUIRES, $mainDeps['css']);
                    $ret->addDependencies(Kwf_Assets_Dependency_Abstract::DEPENDENCY_TYPE_REQUIRES, $dependencies);
                }
            } else {
                $ret = $this->_guessMainFiles($dependencyName);
            }
        } else if (substr($dependencyName, 0, 8) == 'FontFace' && strlen($dependencyName) > 8) {
            //eg. FontFaceIcomoon, FontFaceFontAwesome, FontFaceIonicons
            $bowerName = substr($dependencyName, 8);
            $bowerName[0] = strtolower($bowerName[0]);
            if (file_exists('vendor/bower_components/'.$bowerName.'/fonts.css')) {
                $ret = new Kwf_Assets_Dependency_File_Css($this->_providerList, $bowerName.'/fonts.css');
            } else if (file_exists('vendor/bower_components/'.$bowerName.'-font/fonts.css')) {
                $ret = new Kwf_Assets_Dependency_File_Css($this->_providerList, $bowerName.'-font/fonts.css');
            } else if (file_exists('vendor/bower_components/'.$bowerName.'-fonts/fonts.css')) {
                $ret = new Kwf_Assets_Dependency_File_Css($this->_providerList, $bowerName . '-fonts/fonts.css');
            } else if (file_exists('vendor/bower_components/'.$bowerName.'-font/css/'.$bowerName.'-font.css')) {
                $ret = new Kwf_Assets_Dependency_File_Css($this->_providerList, $bowerName.'-font/css/'.$bowerName.'-font.css');

            } else if (file_exists('vendor/bower_components/'.$bowerName)) {
                $ret = new Kwf_Assets_Dependency_FontFace($this->_providerList, $bowerName, $bowerName);
            } else if (file_exists('vendor/bower_components/'.$bowerName.'-font')) {
                $ret = new Kwf_Assets_Dependency_FontFace($this->_providerList, $bowerName, $bowerName.'-font');
            } else if (file_exists('vendor/bower_components/'.$bowerName.'-fonts')) {
                $ret = new Kwf_Assets_Dependency_FontFace($this->_providerList, $bowerName, $bowerName.'-fonts');
            } else {
                throw new Kwf_Exception("Can't find font dependency for $dependencyName");
            }
        } else if  (substr(strtolower($dependencyName), 0, strlen($this->_path)+1) == strtolower($this->_path).'/') {
            //absolute path to single file path given
            return new Kwf_Assets_Dependency_File_Js($this->_providerList, $dependencyName.'.js');
        }
        return $ret;
    }

    public function getPathTypes()
    {
        $ret = array();
        foreach (glob(VENDOR_PATH.'/bower_components/*') as $i) {
            $type = substr($i, strlen(VENDOR_PATH.'/bower_components/'));
            if (substr($type, -3) == '.js') $type = substr($type, 0, -3);
            if (substr($type, -2) == 'js') {
                $ret[$type] = $i;
                $type = substr($type, 0, -2);
            }
            $ret[$type] = $i;
        }
        return $ret;
    }
}
