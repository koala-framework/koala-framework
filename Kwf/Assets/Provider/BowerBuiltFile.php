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
                    $jsDep = new Kwf_Assets_Dependency_File_JsPreBuilt($type.'/'.$f['file'], $type.'/'.$baseFileName.'.min.js', $type.'/'.$baseFileName.'.min.map');
                } else {
                    $jsDep = new Kwf_Assets_Dependency_File_Js($type.'/'.$f['file']);
                }
                $deps = array();
                foreach ($f['additionalFiles'] as $i) {
                    if (file_exists($dir.'/'.$i)) {
                        if (substr($i, -4) == '.css') {
                            $deps[] = new Kwf_Assets_Dependency_File_Css($type.'/'.$i);
                        } else if (substr($i, -3) == '.js') {
                            $deps[] = new Kwf_Assets_Dependency_File_Js($type.'/'.$i);
                        }
                    }
                }
                if ($deps) {
                    array_unshift($deps, $jsDep);
                    $ret = new Kwf_Assets_Dependency_Dependencies($deps, $dependencyName);
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
        if (strtolower($dependencyName) == $this->_path || strtolower($dependencyName).'.js' == $this->_path) {
            $type = $this->_path;
            $dir = VENDOR_PATH.'/bower_components/'.$this->_path;
            if (file_exists($dir.'/bower.json')) {
                $ret = new Kwf_Assets_Dependency_Dependencies(array(), $dependencyName);
                $bower = json_decode(file_get_contents($dir.'/bower.json'), true);
                if (isset($bower['main'])) {
                    $main = $bower['main'];
                    if (is_string($main)) $main = array($main);
                    foreach ($main as $mainFile) {
                        $mainFile = $mainFile;
                        if (substr($mainFile, -3) == '.js') {
                            if (substr($mainFile, -7) == '.min.js' || substr($mainFile, -7) == '-min.js') {
                                //we don't want to use min file
                                $mainFile = substr($mainFile, 0, -7).'.js';
                            }
                            if (file_exists($dir.'/'.substr($mainFile, 0, -3).'.min.js') && file_exists($dir.'/'.substr($mainFile, 0, -3).'.min.map')) {
                                //use shipped minimied+map file if exists
                                $d = new Kwf_Assets_Dependency_File_JsPreBuilt($type.'/'.$mainFile, $type.'/'.substr($mainFile, 0, -3).'.min.js', $type.'/'.substr($mainFile, 0, -3).'.min.map');
                            } elseif (file_exists($dir.'/'.substr($mainFile, 0, -3).'-min.js') && file_exists($dir.'/'.substr($mainFile, 0, -3).'-min.map')) {
                                //use shipped minimied+map file if exists
                                $d = new Kwf_Assets_Dependency_File_JsPreBuilt($type.'/'.$mainFile, $type.'/'.substr($mainFile, 0, -3).'-min.js', $type.'/'.substr($mainFile, 0, -3).'-min.map');
                            } else {
                                $d = new Kwf_Assets_Dependency_File_Js($type.'/'.$mainFile);
                            }
                            $ret->addDependency(Kwf_Assets_Dependency_Abstract::DEPENDENCY_TYPE_REQUIRES, $d);
                        } elseif (substr($mainFile, -4) == '.css') {
                            $d = new Kwf_Assets_Dependency_File_Css($type.'/'.$mainFile);
                            $ret->addDependency(Kwf_Assets_Dependency_Abstract::DEPENDENCY_TYPE_REQUIRES, $d);
                        }
                    }
                } else {
                    throw new Kwf_Exception("No main property in $dir/bower.json");
                }
                if (isset($bower['dependencies'])) {
                    foreach ($bower['dependencies'] as $depName=>$version) {
                        $d = $this->_providerList->findDependency($depName);
                        if (!$d) {
                            throw new Kwf_Exception("Can't find dependency '$depName'");
                        }
                        $ret->addDependency(Kwf_Assets_Dependency_Abstract::DEPENDENCY_TYPE_REQUIRES, $d);
                    }
                }
            } else {
                $ret = $this->_guessMainFiles($dependencyName);
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
                $ret = new Kwf_Assets_Dependency_File_Css($bowerName . '-fonts/fonts.css');
            } else if (file_exists('vendor/bower_components/'.$bowerName.'-font/css/'.$bowerName.'-font.css')) {
                $ret = new Kwf_Assets_Dependency_File_Css($bowerName.'-font/css/'.$bowerName.'-font.css');

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
