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

    public function getDependenciesForDependency($dependency)
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
            $cacheId = str_replace(array('/', '.', '-'), '_', $src['file']).'__'.md5_file($src['file']);
        } else if ($src['type'] == 'contents') {
            $cacheId = md5($src['contents']);
        } else {
            throw new Kwf_Exception_NotYetImplemented();
        }
        $deps = self::_getCache()->load($cacheId);
        if ($deps === false) {
            if ($src['type'] == 'file') {
                $deps = Kwf_Assets_CommonJs_Parser::parse($src['file']);
            } else if ($src['type'] == 'contents') {
                $temp = tempnam('temp/', 'commonjs');
                file_put_contents($temp, $src['contents']);
                $deps = Kwf_Assets_CommonJs_Parser::parse($temp);
                unlink($temp);
            }
            self::_getCache()->save($deps, $cacheId);
        }

        foreach ($deps as $dep) {
            if (substr($dep, 0, 2) == './') {
                $fn = $dependency->getFileNameWithType();
                $dir = substr($fn, 0, strrpos($fn, '/')+1);
                $dep = $dir . substr($dep, 2);
            }
            $d = $this->_providerList->findDependency($dep);
            if (!$d) throw new Kwf_Exception("Can't resolve dependency: require '$dep' for $dependency");
            $ret[$dep] = $d;
            foreach ($this->_parseDependencies($d) as $index=>$i) {
                $d->addDependency(Kwf_Assets_Dependency_Abstract::DEPENDENCY_TYPE_COMMONJS, $i, $index);
                $requires = $d->getDependencies(Kwf_Assets_Dependency_Abstract::DEPENDENCY_TYPE_REQUIRES);
                $indx = array_search($i, $requires, true);
                if ($indx) {
                    unset($requires[$indx]);
                    $d->setDependencies(Kwf_Assets_Dependency_Abstract::DEPENDENCY_TYPE_REQUIRES, $requires);
                }
            }
        }
        return $ret;
    }
}
