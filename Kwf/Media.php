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
        $prefix = '';
        if ($r = Kwf_Component_Data_Root::getInstance()) {
            if ($r->filename) {
                $prefix = '/'.$r->filename;
            }
        }
        if (is_null($time)) {
            $time = Kwf_Media_MtimeCache::getInstance()->loadOrCreate($class, $id, $type);
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
            Kwf_Media_OutputCache::getInstance()->remove($cacheId);
            Kwf_Media_MtimeCache::getInstance()->remove($class, $id, $type);
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

        $output = Kwf_Media_OutputCache::getInstance()->load($cacheId);

        if ($output && !isset($output['file']) && !isset($output['contents'])) {
            //cache entry from older kwf version where file was not set
            $output = false;
        }
        if (isset($output['file']) && !file_exists($output['file'])) $output = false;

        if ($output && isset($output['mtimeFiles'])) {
            foreach ($output['mtimeFiles'] as $f) {
                if (filemtime($f) > $output['mtime']) {
                    Kwf_Media_OutputCache::getInstance()->remove($cacheId);
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
            if (isset($output['file']) && $output['file'] instanceof Kwf_Uploads_Row) {
                $output['file'] = $output['file']->getFileSource();
            }
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
            $disableMediaCache = array_key_exists('disableMediaCache', $output) && $output['disableMediaCache'];
            if (!$disableMediaCache && $useCache) {
                if (isset($output['contents']) && strlen($output['contents']) > 20*1024) {
                    //don't cache contents larger than 20k in apc, use separate file cache
                    $cacheFileName = self::_generateCacheFilePath($class, $id, $type);
                    if (!is_dir(dirname($cacheFileName))) mkdir(dirname($cacheFileName), 0777, true);
                    file_put_contents($cacheFileName, $output['contents']);
                    $output['file'] = $cacheFileName;
                    unset($output['contents']);
                    Kwf_Util_Media::onCacheFileWrite($cacheFileName);
                }
                Kwf_Media_OutputCache::getInstance()->save($output, $cacheId, $specificLifetime);
            }

        }

        return $output;
    }

    public static function createCacheId($class, $id, $type)
    {
        return str_replace(array('.', '>'), array('___', '____'), $class) . '_' . str_replace('-', '__', $id) . '_' . $type;
    }

    private static function _generateCacheFilePath($class, $id, $type)
    {
        $groupingFolder = substr(md5($id), 0, 3);
        return Kwf_Config::getValue('mediaCacheDir').'/'.$class.'/'.$groupingFolder.'/'.$id.'/'.$type;
    }

    private static function _deleteFolder($path)
    {
        foreach (array_slice(scandir($path), 2) as $fileOrFolder) {
            $fileOrFolderPath = $path.'/'.$fileOrFolder;
            if (is_file($fileOrFolderPath)) {
                unlink($fileOrFolderPath);
            } else {
                self::_deleteFolder($fileOrFolderPath);
            }
        }
        rmdir($path);
    }

    public static function collectGarbage($debug)
    {
        $cacheFolder = Kwf_Config::getValue('mediaCacheDir');
        // get all folders, except . and .. (array_slice)
        $mediaClasses = array_slice(scandir($cacheFolder), 2);
        foreach ($mediaClasses as $mediaClass) {
            if (is_file($cacheFolder.'/'.$mediaClass)) continue;

            // Classname without dot
            $class = strpos($mediaClass, '.') ? substr($mediaClass, 0, strpos($mediaClass, '.')) : $mediaClass;

            if (!class_exists($class)) {
                self::_deleteFolder($cacheFolder.'/'.$mediaClass);
                continue;
            }

            if (!is_instance_of($mediaClass, 'Kwf_Media_Output_ClearCacheInterface')) continue;

            $classFolder = $cacheFolder.'/'.$mediaClass;
            $groups = array_slice(scandir($classFolder), 2);
            foreach ($groups as $group) {
                // only check randomly one out of ten to improve performance
                if (rand(0, 9) !== 0) continue;

                $groupFolder = $classFolder . '/' . $group;
                $ids = array_slice(scandir($groupFolder), 2);
                foreach ($ids as $id) {
                    $idFolder = $groupFolder . '/' . $id;
                    if (is_file($idFolder)) continue; // something old...

                    $canCacheBeDeleted = call_user_func(array($class, 'canCacheBeDeleted'), $id);
                    if (!$canCacheBeDeleted) continue;

                    $types = array_slice(scandir($idFolder), 2);
                    foreach ($types as $type) {
                        unlink(realpath($idFolder . '/' . $type));
                        $cacheId = self::createCacheId($mediaClass, $id, $type);
                        Kwf_Media_OutputCache::getInstance()->remove($cacheId);
                    }
                    rmdir(realpath($idFolder));
                }
            }
        }
    }
}
