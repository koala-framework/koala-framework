<?php
class Kwf_Media
{
    private static $_ouputCache;

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
        $checksum = self::getChecksum($class, $id, $type, $filename);
        $prefix = '';
        if ($r = Kwf_Component_Data_Root::getInstance()) {
            if ($r->filename) {
                $prefix = '/'.$r->filename;
            }
        }
        if (is_null($time)) {
            $cacheId = 'media-output-mtime-'.self::createCacheId($class, $id, $type);
            $time = Kwf_Cache_Simple::fetch($cacheId);
            if (!$time) {
                $time = time();
                Kwf_Cache_Simple::add($cacheId, $time);
            }
        }
        return $prefix.'/media/'.$class.'/'.$id.'/'.$type.'/'.$checksum.'/'.$time.'/'.urlencode($filename);
    }

    public static function getChecksum($class, $id, $type, $filename)
    {
        return Kwf_Util_Hash::hash($class . $id . $type . urldecode($filename));
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
        }
        Zend_Session::writeClose();
        $output = self::_getOutputWithoutCheckingIsValid($class, $id, $type);
        return $output;
    }

    public static function clearCache($class, $id, $type)
    {
        $cacheId = self::createCacheId($class, $id, $type);
        Kwf_Cache_Simple::delete('media-output-'.$cacheId);
        Kwf_Cache_Simple::delete('media-output-mtime-'.$cacheId);
        if (file_exists('cache/media/'.$cacheId)) {
            unlink('cache/media/'.$cacheId);
        }
    }

    private static function _getOutputWithoutCheckingIsValid($class, $id, $type)
    {
        $cacheId = self::createCacheId($class, $id, $type);

        $output = Kwf_Cache_Simple::fetch('media-output-'.$cacheId);

        if ($output && !isset($output['file']) && !isset($output['contents'])) {
            //scaled image is not cached in apc as it might be larger - load from disk
            $output['file'] = 'cache/media/'.$cacheId;
            if (!file_exists($output['file'])) $output = false;
        }

        if ($output && isset($output['mtimeFiles'])) {
            foreach ($output['mtimeFiles'] as $f) {
                if (filemtime($f) > $output['mtime']) {
                    Kwf_Cache_Simple::delete('media-output-'.$cacheId);
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
                    //TODO lifetime isn't respected for this file
                    file_put_contents('cache/media/'.$cacheId, $cacheData['contents']);
                    unset($cacheData['contents']);
                }
                Kwf_Cache_Simple::add('media-output-'.$cacheId, $cacheData, $specificLifetime);
            }
        }

        return $output;
    }

    public static function createCacheId($class, $id, $type)
    {
        return str_replace(array('.', '>'), array('___', '____'), $class) . '_' . str_replace('-', '__', $id) . '_' . $type;
    }
}
