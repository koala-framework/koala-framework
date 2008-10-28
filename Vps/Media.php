<?php
class Vps_Media
{
    const PASSWORD = 'l4Gx8SFe';
    public static function getUrl($class, $id, $type, $filename)
    {
        if ($filename instanceof Vps_Uploads_Row) {
            $filename = $filename->filename . '.' . $filename->extension;
        }
        $checksum = self::getChecksum($class, $id, $type, $filename);
        return '/media/'.$class.'/'.$id.'/'.$type.'/'.$checksum.'/'.$filename;
    }

    public static function getChecksum($class, $id, $type, $filename)
    {
        return md5(self::PASSWORD . $class . $id . $type . $filename);
    }

    public static function getUrlByRow($row, $type, $filename)
    {
        $pk = $row->getModel()->getPrimaryKey();
        return self::getUrl(get_class($row->getModel()), $row->$pk, $type, $filename);
    }

    public static function getOutput($class, $id, $type)
    {
        if (!is_instance_of($class, 'Vps_Media_Output_Interface')) {
            throw new Vps_Exception("Invalid class: {$params['class']}, does not implement Vps_Media_Output_Interface");
        }
        static $cache = null;
        if (!$cache) $cache = new Vps_Assets_Cache(array('checkComponentSettings'=>false));
        $cacheId = $class.'_'.str_replace('-', '__', $id).'_'.$type;
        if (!$output = $cache->load($cacheId)) {
            $output = call_user_func(array($class, 'getMediaOutput'), $id, $type, $class);
            $cache->save($output, $cacheId);
        }
        return $output;
    }
}
