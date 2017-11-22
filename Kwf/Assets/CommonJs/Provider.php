<?php
class Kwf_Assets_CommonJs_Provider extends Kwf_Assets_Provider_Abstract
{
    private $_parsed = array();

    public function __construct()
    {
    }

    public function getDependency($dependencyName)
    {
        return null;
    }

    public function getDependenciesForDependency(Kwf_Assets_Dependency_Abstract $dependency)
    {
        if ($dependency->getMimeType() != 'text/javascript' && $dependency->getMimeType() != 'text/javascript; defer') {
            return array();
        }
        if (!$dependency->isCommonJsEntry()) {
            return array();
        }
        $ret = array(
            Kwf_Assets_Dependency_Abstract::DEPENDENCY_TYPE_COMMONJS => $this->_parseDependencies($dependency)
        );
        return $ret;
    }

    private static function _getCache()
    {
        static $cache;
        if (!isset($cache)) {
            $cache = new Zend_Cache_Core(array(
                'lifetime' => null,
                'write_control' => false,
                'automatic_cleaning_factor' => 0,
                'automatic_serialization' => true,
            ));
            $cache->setBackend(new Kwf_Cache_Backend_File(array(
                'cache_dir' => 'cache/commonjs',
                'hashed_directory_level' => 2,
            )));
        }
        return $cache;
    }

    private function _parseDependencies($dependency)
    {
        if (in_array($dependency, $this->_parsed, true)) return array();

        $this->_parsed[] = $dependency;

        $ret = array();

        $src = $dependency->getContentsSource();
        if ($src['type'] == 'file') {
            $cacheId = str_replace(array('/', '.', '-', '$', '@'), '_', $src['file']).'__'.md5_file($src['file']);
        } else if ($src['type'] == 'contents') {
            $cacheId = md5($src['contents']);
        } else {
            throw new Kwf_Exception_NotYetImplemented();
        }
        $cacheId .= "v2"; //versioned cache id as clear-cache doesn't clear it

        $sourceChanged = false;
        $deps = array();
        $depBrowserAlternatives = array();

        $cachedData = self::_getCache()->load($cacheId);
        if ($cachedData !== false) {
            $deps = $cachedData['deps'];
            $depBrowserAlternatives = $cachedData['alternatives'];
            $sourceChanged = $cachedData['sourceChanged'];
        } else {
            if ($src['type'] == 'file') {
                $contents = file_get_contents($src['file']);
            } else if ($src['type'] == 'contents') {
                $contents = $src['contents'];
            }
            $useBabel = preg_match("/\bimport\s+(?:.+\s+from\s+)?[\'\"]([^\"\']+)[\"\']/", $contents);
            if ($useBabel) {
                $src['type'] = 'contents';
                $src['contents'] = $dependency->getContentsPacked()->getFileContents(); //we have to use complied contents as babel adds require() statements
            }
            if ($src['type'] == 'file') {
                $parsedFile = Kwf_Assets_CommonJs_ModuleDepsParser::parse($src['file']);
                $deps = $parsedFile['deps'];
            } else if ($src['type'] == 'contents') {
                $temp = tempnam('temp/', 'commonjs');
                file_put_contents($temp, $src['contents']);
                $parsedFile = Kwf_Assets_CommonJs_ModuleDepsParser::parse($temp);
                $deps = $parsedFile['deps'];
                unlink($temp);
            }
            if (file_exists("node_modules/" . (string)$dependency)) {
                $dep = (string)$dependency;

                $pos = strpos($dep, "/");
                $dir = "node_modules/" . substr($dep, 0, $pos);
                if (substr($dep, 0, 1) === '@') $dir .= substr($dep, $pos, strpos($dep, "/", $pos));
                $package = json_decode(file_get_contents($dir . '/package.json'), true);
                if (isset($package['browser'])) {
                    if (is_string($package['browser'])) {
                        $depBrowserAlternatives[$package['main']] = $package['browser'];
                    } else {
                        foreach ($package['browser'] as $key => $value) {
                            if (substr($key, 0, 2) == './') $key = $dependency->getType() . substr($key, 1);
                            $key = str_replace('.js', '', $key);
                            if ($value !== false) {
                                if (substr($value, 0, 2) == './') $value = $dependency->getType() . substr($value, 1);
                                $value = str_replace('.js', '', $value);
                            }
                            $depBrowserAlternatives[$key] = $value;
                        }
                    }
                }
            }

            $sourceChanged = $parsedFile['source'] != $contents;
            self::_getCache()->save(array(
                'deps' => $deps,
                'alternatives' => $depBrowserAlternatives,
                'sourceChanged' => $sourceChanged
            ), $cacheId);
        }

        if ($sourceChanged) {
            $dependency->addFilter(new Kwf_Assets_CommonJs_ModuleDepsFilter());
        }

        foreach ($deps as $depName) {
            $dep = $depName;

            if ($depBrowserAlternatives) {
                if (array_key_exists($dep, $depBrowserAlternatives)) {
                    $dep = $depBrowserAlternatives[$dep];
                }
            }

            if (substr($dep, 0, 2) == './') {
                $fn = $dependency->getFileNameWithType();
                $dir = substr($fn, 0, strrpos($fn, '/')+1);
                $dep = $dir . substr($dep, 2);
            } else if (substr($dep, 0, 3) == '../') {
                $fn = $dependency->getFileNameWithType();
                $dir = substr($fn, 0, strrpos($fn, '/'));
                while (substr($dep, 0, 3) == '../') {
                    $dep = substr($dep, 3);
                    $dir = substr($dir, 0, strrpos($dir, '/'));
                }
                $dep = $dir . '/'. $dep;
            }

            $d = null;
            if ($depBrowserAlternatives) {
                $path = substr(Kwf_Assets_Dependency_File::calculateAbsolutePath($dep), 1);
                if (!$path) continue;
                if (array_key_exists($path, $depBrowserAlternatives)) {
                    if (!$depBrowserAlternatives[$path]) {
                        $d = new Kwf_Assets_Dependency_EmptyJs($dep, $this->_providerList);
                    } else {
                        $dep = $depBrowserAlternatives[$path];
                    }
                }
            }

            if (!$d) $d = $this->_providerList->findDependency($dep);
            if (!$d) throw new Kwf_Exception("Can't resolve dependency: require '$depName' => '$dep' for $dependency");
            $ret[$depName] = $d;

            $requires = $d->getDependencies(Kwf_Assets_Dependency_Abstract::DEPENDENCY_TYPE_REQUIRES);
            foreach ($requires as $index=>$r) {
                if ($r->getMimeType() == 'text/javascript') {
                    unset($requires[$index]);
                }
            }
            $d->setDependencies(Kwf_Assets_Dependency_Abstract::DEPENDENCY_TYPE_REQUIRES, $requires);

            foreach ($this->_parseDependencies($d) as $index=>$i) {
                if ($i->getMimeType() == 'text/javascript') {
                    $d->addDependency(Kwf_Assets_Dependency_Abstract::DEPENDENCY_TYPE_COMMONJS, $i, $index);
                } else {
                    //add css dependency twice: 1. empty commonjs (to make boweser-pack happy)
                                              //2. the actual css so it will be included in the css
                    $d->addDependency(Kwf_Assets_Dependency_Abstract::DEPENDENCY_TYPE_COMMONJS, new Kwf_Assets_Dependency_EmptyJs($i->getIdentifier().'Empty', $this->_providerList), $index);
                    $d->addDependency(Kwf_Assets_Dependency_Abstract::DEPENDENCY_TYPE_REQUIRES, $i, $index);
                }

            }

        }
        return $ret;
    }
}
