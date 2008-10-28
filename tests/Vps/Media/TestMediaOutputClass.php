<?php
class Vps_Media_TestMediaOutputClass implements Vps_Media_Output_Interface
{
    public static $called = 0;
    public static $mtimeFiles = array();
    public static function getMediaOutput($id, $type, $className)
    {
        self::$called++;
        if ($type == 'simple') {
            return array(
                'mimeType' => 'text/plain',
                'contents' => 'foobar'.$id
            );
        } else if ($type == 'mtimeFiles') {
            return array(
                'mimeType' => 'text/plain',
                'contents' => 'foobar'.$id,
                'mtimeFiles' => self::$mtimeFiles
            );
        } else if ($type == 'nothing') {
            return null;
        }
    }
}
