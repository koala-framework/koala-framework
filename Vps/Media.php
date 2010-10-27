<?php
class Vps_Media
{
    private static $_ouputCache;
    const PASSWORD = 'l4Gx8SFe';

    public static function getUrl($class, $id, $type, $filename)
    {
        if ($filename instanceof Vps_Uploads_Row) {
            $filename = $filename->filename . '.' . $filename->extension;
        }
        $checksum = self::getChecksum($class, $id, $type, $filename);
        $prefix = '';
        if ($r = Vps_Component_Data_Root::getInstance()) {
            if ($r->filename) {
                $prefix = '/'.$r->filename;
            }
        }
        return $prefix.'/media/'.$class.'/'.$id.'/'.$type.'/'.$checksum.'/'.$filename;
    }

    public static function getChecksum($class, $id, $type, $filename)
    {
        return md5(self::PASSWORD . $class . $id . $type . $filename);
    }

    /**
     * @param Vps_Model_Row_Interface Row zu der ein Bild existiert
     * @param string Type
     * @param string/Vps_Uploads_Row Wenn string wird der Dateiname verwendet
     *         wenn Vps_Uploads_Row wird der Original-Dateiname verwendet
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
            return Vps_Media_Image::calculateScaleDimensions(
                $fileRow->getImageDimensions(),
                $dim
            );
        }
        return null;
    }

    public static function setOutputCache(Zend_Cache_Core $cache)
    {
        self::$_ouputCache = $cache;
    }

    public static function getOutputCache()
    {
        if (!isset(self::$_ouputCache)) {
            self::$_ouputCache = new Vps_Media_Cache();
        }
        return self::$_ouputCache;
    }

    public static function getOutput($class, $id, $type)
    {
        if (!class_exists($class) || !is_instance_of($class, 'Vps_Media_Output_Interface')) {
            // TODO Ev. Mail senden, wenn Grafik nicht ausgeliefert wird
            throw new Vps_Exception_NotFound();
        }

        $cacheId = self::createCacheId($class, $id, $type);
        if (!Vps_Registry::get('config')->debug->mediaCache || !($output = self::getOutputCache()->load($cacheId))) {
            $output = call_user_func(array($class, 'getMediaOutput'), $id, $type, $class);
            $specificLifetime = false;
            if (isset($output['lifetime'])) {
                $specificLifetime = $output['lifetime'];
            }
            if (Vps_Registry::get('config')->debug->mediaCache) {
                self::getOutputCache()->save($output, $cacheId, array(), $specificLifetime);
            } else {
                //browser cache deaktivieren
                $output['lifetime'] = false;
            }
        }
        return $output;
    }

    public static function createCacheId($class, $id, $type)
    {
        return $class . '_' . str_replace('-', '__', $id) . '_' . $type;
    }
}
