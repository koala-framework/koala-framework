<?php
class Kwf_Media
{
    private static $_ouputCache;
    const DONT_HASH_TYPE_PREFIX = 'dh-';

    /**
     *
     * @param string
     * @param string
     * @param string
     * @param string
     * @param int Kann gesetzt werden wenn wir in diesem web auf das bild nicht direkten zugriff haben
     *            sondern nur für ein anderes web die url generieren
     */
    public static function getUrl($class, $id, $type, $filename, $time = null)
    {
        if ($filename instanceof Kwf_Uploads_Row) {
            $filename = $filename->filename . '.' . $filename->extension;
        }
        if ($filename == '.') $filename = '';

        //Replace Slashes and Backslashes with an underscore
        //Otherwise we would get a wrong url
        //e.g. $filename = foo/bar.png -> /media/FooModel/1/default/ab123/1234/foo/bar.png
        $filename = str_replace('/', '_', $filename);
        $filename = str_replace('\\', '_', $filename);
        $checksumType = $type;
        if (substr($type, 0, strlen(Kwf_Media::DONT_HASH_TYPE_PREFIX)) == Kwf_Media::DONT_HASH_TYPE_PREFIX) {
            $checksumType = Kwf_Media::DONT_HASH_TYPE_PREFIX;
        }
        $class = rawurlencode($class);
        $checksum = self::getChecksum($class, $id, $checksumType, rawurlencode($filename));
        $prefix = Kwf_Setup::getBaseUrl();
        if ($r = Kwf_Component_Data_Root::getInstance()) {
            if ($r->filename) {
                $prefix .= '/'.$r->filename;
            }
        }
        if (is_null($time)) {
            $cacheId = 'mtime-'.self::createCacheId($class, $id, $type);
            $time = Kwf_Media_MemoryCache::getInstance()->load($cacheId);
            if (!$time) {
                $time = time();
                Kwf_Media_MemoryCache::getInstance()->save($time, $cacheId);
            }
        }
        return $prefix.'/media/'.$class.'/'.$id.'/'.$type.'/'.$checksum.'/'.$time.'/'.rawurlencode($filename);
    }

    public static function getChecksum($class, $id, $type, $encodedFilename)
    {
        if (substr($type, 0, strlen(Kwf_Media::DONT_HASH_TYPE_PREFIX)) == Kwf_Media::DONT_HASH_TYPE_PREFIX) {
            $type = Kwf_Media::DONT_HASH_TYPE_PREFIX;
        }
        return substr(Kwf_Util_Hash::hash($class . $id . $type . rawurldecode($encodedFilename)), 0, 8);
    }

    /**
     * @param Kwf_Model_Row_Interface Row zu der ein Bild existiert
     * @param string Type
     * @param string/Kwf_Uploads_Row Wenn string wird der Dateiname verwendet
     *         wenn Kwf_Uploads_Row wird der Original-Dateiname verwendet
     *         wenn nicht gesetzt wird die uploads row mittels $type ermittelt
     *         und dieser Dateiname verwendet
     */
    public static function getUrlByRow($row, $type, $filename = null)
    {
        $pk = $row->getModel()->getPrimaryKey();
        if (!$filename) {
            $filename = $row->getParentRow($type);
        }
        return self::getUrl(get_class($row->getModel()), $row->$pk, $type, $filename);
    }

    public static function getDimensionsByRow($row, $type, $fileRow = null)
    {
        $model = get_class($row->getModel());
        $dim = call_user_func(array($model, 'getImageDimensions'), $type);
        if (!$fileRow) {
            $fileRow = $row->getParentRow($type);
        }
        if ($fileRow) {
            return Kwf_Media_Image::calculateScaleDimensions(
                $fileRow->getImageDimensions(),
                $dim
            );
        }
        return null;
    }

    public static function getOutput($class, $id, $type)
    {
        $cacheId = 'media-isvalid-'.self::createCacheId($class, $id, $type);
        if (!Kwf_Cache_Simple::fetch($cacheId)) {
            $classWithoutDot = strpos($class, '.') ? substr($class, 0, strpos($class, '.')) : $class;
            if (!Kwf_Loader::isValidClass($classWithoutDot)) throw new Kwf_Exception_NotFound();
            $isValid = Kwf_Media_Output_IsValidInterface::VALID;
            if (is_instance_of($classWithoutDot, 'Kwf_Media_Output_IsValidInterface')) {
                $isValid = call_user_func(array($classWithoutDot, 'isValidMediaOutput'), $id, $type, $class);
                if ($isValid == Kwf_Media_Output_IsValidInterface::INVALID) {
                    throw new Kwf_Exception_NotFound();
                } else if ($isValid == Kwf_Media_Output_IsValidInterface::ACCESS_DENIED) {
                    throw new Kwf_Exception_AccessDenied();
                } else if ($isValid == Kwf_Media_Output_IsValidInterface::VALID) {
                } else if ($isValid == Kwf_Media_Output_IsValidInterface::VALID_DONT_CACHE) {
                } else {
                    throw new Kwf_Exception("unknown isValidMediaOutput return value");
                }
            }
            if ($isValid != Kwf_Media_Output_IsValidInterface::VALID_DONT_CACHE) {
                Kwf_Cache_Simple::add($cacheId, true, 60*60);
            }
        } else {
            $isValid = Kwf_Media_Output_IsValidInterface::VALID;
        }
        Zend_Session::writeClose();
        $output = self::_getOutputWithoutCheckingIsValid($class, $id, $type);
        if ($isValid == Kwf_Media_Output_IsValidInterface::VALID_DONT_CACHE) {
            $output['lifetime'] = false; //for valid don't cache also don't output cache http headers (to avoid proxy or browser caching)
        }
        return $output;
    }

    /**
     *
     * @param string
     * @param string
     * @param string|array array to clear multiple types
     */
    public static function clearCache($class, $id, $types)
    {
        if (!is_array($types)) $types = array($types);

        foreach ($types as $type) {
            $cacheId = self::createCacheId($class, $id, $type);
            Kwf_Media_MemoryCache::getInstance()->remove($cacheId);
            Kwf_Media_MemoryCache::getInstance()->remove('mtime-'.$cacheId);
            //not required to delete cache/media/$cacheId, that will be regenerated if $cacheId is deleted
        }
    }

    public static function getOutputWithoutCheckingIsValid($class, $id, $type)
    {
        return self::_getOutputWithoutCheckingIsValid($class, $id, $type);
    }

    private static function _getOutputWithoutCheckingIsValid($class, $id, $type)
    {
        $cacheId = self::createCacheId($class, $id, $type);

        $output = Kwf_Media_MemoryCache::getInstance()->load($cacheId);

        if ($output && !isset($output['file']) && !isset($output['contents'])) {
            //cache entry from older kwf version where file was not set
            $output = false;
        }
        if (isset($output['file']) && !file_exists($output['file'])) $output = false;

        if ($output && isset($output['mtimeFiles'])) {
            foreach ($output['mtimeFiles'] as $f) {
                if (filemtime($f) > $output['mtime']) {
                    Kwf_Media_MemoryCache::getInstance()->remove($cacheId);
                    $output = false;
                    break;
                }
            }
        }

        if (!$output) {
            $classWithoutDot = strpos($class, '.') ? substr($class, 0, strpos($class, '.')) : $class;
            if (!Kwf_Loader::isValidClass($classWithoutDot) || !is_instance_of($classWithoutDot, 'Kwf_Media_Output_Interface')) {
                throw new Kwf_Exception_NotFound();
            }
            $output = call_user_func(array($classWithoutDot, 'getMediaOutput'), $id, $type, $class);
            $specificLifetime = false;
            $useCache = true;
            if (isset($output['lifetime'])) {
                $specificLifetime = $output['lifetime'];
                if (!$output['lifetime']) {
                    $useCache = false;
                }
            }
            if (!isset($output['mtime'])) {
                if (isset($output['file'])) {
                    $output['mtime'] = filemtime($output['file']);
                } else if (isset($output['mtimeFiles'])) {
                    $output['mtime'] = 0;
                    foreach ($output['mtimeFiles'] as $f) {
                        $output['mtime'] = max($output['mtime'], filemtime($f));
                    }
                } else {
                    $output['mtime'] = time();
                }
            }
            if ($useCache) {
                $cacheData = $output;
                if (isset($cacheData['contents']) && strlen($cacheData['contents']) > 20*1024) {
                    //don't cache contents larger than 20k in apc, use separate file cache
                    $cacheFileName = 'cache/media/'.$class.'/'.$id.'/'.$type;
                    if (!is_dir(dirname($cacheFileName))) @mkdir(dirname($cacheFileName), 0777, true);
                    file_put_contents($cacheFileName, $cacheData['contents']);
                    $cacheData['file'] = $cacheFileName;
                    unset($cacheData['contents']);
                }
                Kwf_Media_MemoryCache::getInstance()->save($cacheData, $cacheId, $specificLifetime);
            }
        }

        return $output;
    }

    public static function createCacheId($class, $id, $type)
    {
        return str_replace(array('.', '>'), array('___', '____'), $class) . '_' . str_replace('-', '__', $id) . '_' . $type;
    }
}
