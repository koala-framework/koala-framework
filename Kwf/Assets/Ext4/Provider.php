<?php
class Kwf_Assets_Ext4_Provider extends Kwf_Assets_Provider_Abstract
{
    private static function _getAliasClasses()
    {
        static $classes;
        if (isset($classes)) return $classes;
        $classes = array();
        $t = microtime(true);
        $it = new RecursiveIteratorIterator(new RecursiveDirectoryIterator(Kwf_Config::getValue('path.ext4').'/src'), RecursiveIteratorIterator::LEAVES_ONLY);
        foreach ($it as $i) {
            $depName = 'Ext4.'.str_replace('/', '.', substr($i->getPathname(), strlen(Kwf_Config::getValue('path.ext4').'/src/'), -3));
            $fileContents = file_get_contents($i->getPathname());
            if (preg_match_all('#^\s*(//|\*) @(class|alternateClassName|define) ([a-zA-Z0-9\./]+)\s*$#m', $fileContents, $m)) {
                foreach ($m[3] as $cls) {
                    $classes[$cls] = $depName;
                }
            }

            // remove comments to avoid dependencies from docs/examples
            $fileContents = preg_replace('!/\*[^*]*\*+([^/][^*]*\*+)*'.'/!', '', $fileContents);

            if (preg_match_all('#Ext\.define\(\s*([\'"])([^\'"]+)\1#', $fileContents, $m)) {
                foreach ($m[2] as $cls) {
                    $classes[$cls] = $depName;
                }
            }
            if (preg_match('#^\s*(alternateClassName):\s*\'([a-zA-Z0-9\.]+)\'\s*,?\s*$#m', $fileContents, $m)) {
                $classes[$m[2]] = $depName;
            }
            if (preg_match('#^\s*(alternateClassName):\s*\[([^\]]+)\]\s*,?\s*$#m', $fileContents, $m)) {
                if (preg_match_all('#\'([a-zA-Z0-9\._]+)\'#', $m[2], $m2)) {
                    foreach ($m2[1] as $i) {
                        $classes[$i] = $depName;
                    }
                }

            }
        }
        return $classes;
    }

    public function getDependency($dependencyName)
    {
        /*if ($dependencyName == 'Ext4Corex') {

            $files = array(
                'Ext4.class.Loader'
            );
            foreach ($files as $f) {
                $d = $this->_providerList->findDependency($f);
                if (!$d) throw new Kwf_Exception("Can't resolve dependency: extend $cls");
                $deps[] = $d;
            }
            return new Kwf_Assets_Dependency_Dependencies($deps, $dependencyName);
        } else*/
        if (substr($dependencyName, 0, 4) == 'Ext4') {
            $class = substr($dependencyName, 4);
            $file = Kwf_Config::getValue('path.ext4').'/src'.str_replace('.', '/', $class).'.js';
            if (!file_exists($file)) return null;

            return new Kwf_Assets_Ext4_JsDependency($file);
        }
    }

    public function getDependenciesForDependency(Kwf_Assets_Dependency_Abstract $dependency)
    {
        if (!$dependency instanceof Kwf_Assets_Dependency_File_Js && !$dependency instanceof Kwf_Assets_Ext4_JsDependency) {
            return array();
        }
        $deps = array(
            Kwf_Assets_Dependency_Abstract::DEPENDENCY_TYPE_REQUIRES => array(),
            Kwf_Assets_Dependency_Abstract::DEPENDENCY_TYPE_USES => array(),
        );

        if ($dependency->getFileName() == Kwf_Config::getValue('path.ext4').'/src/panel/Panel.js') {
            //$deps[Kwf_Assets_Dependency_Abstract::DEPENDENCY_TYPE_REQUIRES][] = new Kwf_Assets_Ext4_CssDependency('ext4/resources/ext-theme-classic-sandbox/ext-theme-classic-all.css');
            $deps[Kwf_Assets_Dependency_Abstract::DEPENDENCY_TYPE_REQUIRES][] = new Kwf_Assets_Ext4_CssDependency('ext4/resources/ext-theme-neptune/ext-theme-neptune-all.css');
        }


        $fileContents = $dependency->getContents('en');

        // remove comments to avoid dependencies from docs/examples
        $fileContents = preg_replace('!/\*[^*]*\*+([^/][^*]*\*+)*'.'/!', '', $fileContents);


        $aliasClasses = self::_getAliasClasses();

        if (preg_match_all('#^\s*'.'// @require\s+([a-zA-Z0-9\./\-_]+)\s*$#m', $fileContents, $m)) {
            foreach ($m[1] as $f) {
                if (substr($f, -3) == '.js') {
                    $f = substr($f, 0, -3);
                    $curFile = $dependency->getFileName();
                    $curFile = substr($curFile, 0, strrpos($curFile, '/')+1);

                    while (substr($f, 0, 3) == '../') {
                        $f = substr($f, 3);
                        $curFile = substr($curFile, 0, strrpos($curFile, '/', -2)+1);
                    }

                    $f = $curFile . $f;
                    static $paths;
                    if (!isset($paths)) $paths = Kwf_Config::getValueArray('path');
                    $found = false;
                    foreach ($paths as $k=>$i) {
                        if (substr($f, 0, strlen($i)+1) == $i.'/') {
                            $f = substr($f, strlen($i)+1);
                            if (substr($f, 0, 6) == 'tests/') {
                                $f = substr($f, 6);
                            } else if ($k == 'ext4' && substr($f, 0, 4) == 'src/') {
                                $f = 'Ext4.'.substr($f, 4);
                            }
                            $found = true;
                            break;
                        }
                    }
                    if (!$found) throw new Kwf_Exception('path not found');
                    $f = str_replace('/', '.', $f);

                } else {
                    if (substr($f, 0, 5) == 'Ext4.') {
                        $f = 'Ext.'.substr($f, 5);
                    }
                    if (substr($f, 0, 4) == 'Ext.') {
                        $f = $aliasClasses[$f];
                    }
                }

                if ($dependency->getFileName() == Kwf_Config::getValue('path.ext4').'/src/util/Offset.js') {
                    if ($f == 'Ext4.dom.CompositeElement') {
                        $f = null;
                    }
                }

                if ($f) {
                    $d = $this->_providerList->findDependency($f);
                    if (!$d) throw new Kwf_Exception("Can't resolve dependency: require $f");
                    $deps[Kwf_Assets_Dependency_Abstract::DEPENDENCY_TYPE_REQUIRES][] = $d;
                }
            }
        }

        $classes = array(
            Kwf_Assets_Dependency_Abstract::DEPENDENCY_TYPE_USES => array(),
            Kwf_Assets_Dependency_Abstract::DEPENDENCY_TYPE_REQUIRES => array(),
        );
        if (preg_match('#Ext4?\.require\(\s*\'([a-zA-Z0-9\.]+)\'#', $fileContents, $m)) {
            $classes['requires'][] = $m[1];
        }

        if (preg_match('#Ext4?\.define\(\s*[\'"]#', $fileContents, $m)) {
            if (preg_match_all('#^\s*(extend|override|requires|mixins|uses):\s*\'([a-zA-Z0-9\.]+)\'\s*,?\s*$#m', $fileContents, $m)) {
                foreach ($m[2] as $k=>$cls) {
                    $type = ($m[1][$k] == 'uses' ? Kwf_Assets_Dependency_Abstract::DEPENDENCY_TYPE_USES : Kwf_Assets_Dependency_Abstract::DEPENDENCY_TYPE_REQUIRES);
                    $classes[$type][] = $cls;
                }
            }

            if (preg_match_all('#^\s*(requires|mixins|uses):\s*(\[.+?\]|{.+?})\s*,?\s*$#ms', $fileContents, $m)) {
                foreach ($m[2] as $k=>$i) {
                    $type = ($m[1][$k] == 'uses' ? Kwf_Assets_Dependency_Abstract::DEPENDENCY_TYPE_USES : Kwf_Assets_Dependency_Abstract::DEPENDENCY_TYPE_REQUIRES);
                    if (preg_match_all('#\'([a-zA-Z0-9\._]+)\'#', $i, $m2)) {
                        $classes[$type] = array_merge($classes[$type], $m2[1]);
                    }
                }
            }
        }
        foreach ($classes as $type=>$i) {
            foreach ($i as $cls) {
                if (substr($cls, 0, 5) == 'Ext4.') {
                    $cls = 'Ext.'.substr($cls, 5);
                }
                if (substr($cls, 0, 4) == 'Ext.') {
                    $cls = $aliasClasses[$cls];
                }
                $d = $this->_providerList->findDependency($cls);
                if (!$d) throw new Kwf_Exception("Can't resolve dependency: extend $cls");
                $deps[$type][] = $d;
            }
        }

        return $deps;
    }
}
