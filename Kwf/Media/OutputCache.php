<?php
/**
 * Cache for media output
 *
 * Will NOT get deleted on clear-cache
 */
class Kwf_Media_OutputCache
{
    /**
     * @var Zend_Cache_Core
     */
    private $_secondLevelCache = null;
    /**
     * @var Kwf_Media_OutputCache
     */
    private static $_instance;


    public static function getInstance()
    {
        if (!isset(self::$_instance)) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    public static function setInstance($instance)
    {
        self::$_instance = $instance;
    }

    private static function _getBackend()
    {
        static $be = null;
        if (!isset($be)) {
            $be = Kwf_Config::getValue('mediaOutputCacheBackend');
            if (!$be) {
                $be = Kwf_Cache_Simple::getBackend();
            }
        }
        return $be;
    }

    private function _getSecondLevelCache()
    {
        if (!$this->_secondLevelCache) {
            $c = new Zend_Cache_Core(array(
                'lifetime' => null,
                'write_control' => false,
                'automatic_cleaning_factor' => 0,
                'automatic_serialization' => true,
            ));
            $c->setBackend(new Kwf_Media_OutputCacheFileBackend(array(
                'cache_dir' => Kwf_Config::getValue('mediametaCacheDir'),
                'hashed_directory_level' => 2,
            )));
            $this->_secondLevelCache = $c;
        }
        return $this->_secondLevelCache;
    }

    private static function _processCacheId($cacheId)
    {
        return preg_replace('#[^a-zA-Z0-9_]#', '_', $cacheId);
    }

    public function load($id)
    {
        $id = self::_processCacheId($id);
        $be = self::_getBackend();
        if ($be == 'memcached') {
            static $prefix;
            if (!isset($prefix)) $prefix = Kwf_Cache_Simple::getUniquePrefix().'-media-';
            $ret = Kwf_Cache_Simple::getMemcached()->get($prefix.$id);
        } else if ($be == 'memcache') {
            static $prefix;
            if (!isset($prefix)) $prefix = Kwf_Cache_Simple::getUniquePrefix().'-media-';
            $ret = Kwf_Cache_Simple::getMemcache()->get($prefix.$id);
        } else if ($be == 'redis') {
            $ret = Kwf_Cache_Simple::getRedis()->get('media:'.$id);
            if ($ret !== false) $ret = unserialize($ret);
        } else if ($be == 'file') {
            //use secondlevel cache only
            $ret = false;
        } else {
            $ret = Kwf_Cache_Simple::fetch('media-'.$id);
        }
        if ($ret === false) {
            $ret = $this->_getSecondLevelCache()->load($id);
            if ($ret && $be != 'file') {
                //first level empty, refill from second level contents
                $metaDatas = $this->_getSecondLevelCache()->getMetadatas($id);
                $ttl = $metaDatas['expire'] ? $metaDatas['expire']-$metaDatas['mtime'] : 0;
                if ($be == 'memcache') {
                    $flags = is_int($ret) ? null : MEMCACHE_COMPRESSED;
                    Kwf_Cache_Simple::getMemcache()->set($prefix.$id, $ret, $flags, $ttl);
                } else if ($be == 'redis') {
                    if (!$ttl) $ttl = 365*24*60*60;
                    Kwf_Cache_Simple::getRedis()->setEx('media:'.$id, $ttl, serialize($ret));
                } else {
                    Kwf_Cache_Simple::add('media-'.$id, $ret, $ttl);
                }
            }
        }
        return $ret;
    }

    public function save($data, $id, $ttl = null)
    {
        $id = self::_processCacheId($id);
        $this->_getSecondLevelCache()->save($data, $id, array(), $ttl);
        $be = self::_getBackend();
        if ($be == 'memcached') {
            static $prefix;
            if (!isset($prefix)) $prefix = Kwf_Cache_Simple::getUniquePrefix().'-media-';
            return Kwf_Cache_Simple::getMemcached()->set($prefix.$id, $data, $ttl);
        } else if ($be == 'memcache') {
            static $prefix;
            if (!isset($prefix)) $prefix = Kwf_Cache_Simple::getUniquePrefix().'-media-';
            $flags = is_int($data) ? null : MEMCACHE_COMPRESSED;
            return Kwf_Cache_Simple::getMemcache()->set($prefix.$id, $data, $flags, $ttl);
        } else if ($be == 'redis') {
            if (!$ttl) $ttl = 365*24*60*60;
            return Kwf_Cache_Simple::getRedis()->setEx('media:'.$id, $ttl, serialize($data));
        } else if ($be == 'file') {
            //use secondlevel cache only
            return true;
        } else {
            return Kwf_Cache_Simple::add('media-'.$id, $data, $ttl);
        }

    }

    public function remove($id)
    {
        $id = self::_processCacheId($id);
        $this->_getSecondLevelCache()->remove($id);
        $be = self::_getBackend();
        if ($be == 'memcached') {
            static $prefix;
            if (!isset($prefix)) $prefix = Kwf_Cache_Simple::getUniquePrefix().'-media-';
            return Kwf_Cache_Simple::getMemcached()->delete($prefix.$id);
        } else if ($be == 'memcache') {
            static $prefix;
            if (!isset($prefix)) $prefix = Kwf_Cache_Simple::getUniquePrefix().'-media-';
            return Kwf_Cache_Simple::getMemcache()->delete($prefix.$id);
        } else if ($be == 'redis') {
            return Kwf_Cache_Simple::getRedis()->delete('media:'.$id);
        } else if ($be == 'file') {
            return true;
        } else {
            return Kwf_Cache_Simple::delete('media-'.$id);
        }
    }

    public function clean()
    {
        $be = self::_getBackend();
        if ($be == 'memcached') {
            $prefix = Kwf_Cache_Simple::getUniquePrefix().'-media-';
            foreach ($this->_getSecondLevelCache()->getIds() as $id) {
                Kwf_Cache_Simple::getMemcached()->delete($prefix.$id);
            }
        } else if ($be == 'memcache') {
            $prefix = Kwf_Cache_Simple::getUniquePrefix().'-media-';
            foreach ($this->_getSecondLevelCache()->getIds() as $id) {
                Kwf_Cache_Simple::getMemcache()->delete($prefix.$id);
            }
        } else if ($be == 'redis') {
            $prefixLength = strlen(Kwf_Cache_Simple::getRedis()->_prefix(''));
            $it = null;
            while ($keys = Kwf_Cache_Simple::getRedis()->scan($it, Kwf_Cache_Simple::getRedis()->_prefix('media:*'))) {
                foreach ($keys as $k=>$i) {
                    $keys[$k] = substr($i, $prefixLength);
                }
                Kwf_Cache_Simple::getRedis()->delete($keys);
            }
        } else if ($be == 'file') {
            //use secondlevel cache only
        } else {
            foreach ($this->_getSecondLevelCache()->getIds() as $id) {
                Kwf_Cache_Simple::delete('media-'.$id);
            }
        }
        $this->_getSecondLevelCache()->clean();

        $files = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator(Kwf_Config::getValue('mediaCacheDir'), RecursiveDirectoryIterator::SKIP_DOTS),
            RecursiveIteratorIterator::CHILD_FIRST
        );
        foreach ($files as $fileinfo) {
            if ($fileinfo->getFilename() == '.gitignore') continue;
            $todo = ($fileinfo->isDir() ? 'rmdir' : 'unlink');
            $todo($fileinfo->getRealPath());
        }
    }

    public function clear($class)
    {
        $cacheFolder = Kwf_Config::getValue('mediametaCacheDir');
        // get all folders, except . and .. (array_slice)
        $firstLevelFolders = array_slice(scandir($cacheFolder), 2);
        foreach ($firstLevelFolders as $firstLevelFolder) {
            if (is_file($cacheFolder.'/'.$firstLevelFolder)) continue;
            $secondLevelFolders = array_slice(scandir($cacheFolder.'/'.$firstLevelFolder), 2);
            foreach ($secondLevelFolders as $secondLevelFolder) {
                $ids = array_slice(scandir($cacheFolder.'/'.$firstLevelFolder.'/'.$secondLevelFolder), 2);
                foreach ($ids as $id) {
                    if (strpos($id, 'internal-metadatas') !== false) continue;
                    if (strpos($id, $class) === false) continue;
                    $id = substr($id, 13);
                    $data = $this->_getSecondLevelCache()->load($id);
                    if (isset($data['file']) && $data['file']) {
                        unlink(realpath($data['file']));
                    }
                    $this->remove($id);
                }
            }
        }
    }
}
