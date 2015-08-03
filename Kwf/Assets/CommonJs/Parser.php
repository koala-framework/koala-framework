<?php
class Kwf_Assets_CommonJs_Parser
{
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

    public static function parse($filename)
    {
        if (substr($filename, 0, 5) == 'temp/') {
            $cacheId = md5_file($filename);
        } else {
            $cacheId = str_replace(array('/', '.', '-'), '_', $filename).'__'.md5_file($filename);
        }
        $ret = self::_getCache()->load($cacheId);
        if ($ret === false) {
            $cmd = getcwd()."/".VENDOR_PATH."/bin/node ".__DIR__."/Parser.js ".$filename;
            $process = new Symfony\Component\Process\Process($cmd);
            $process->mustRun();
            $out = json_decode($process->getOutput(), true);
            $ret = array();
            foreach (array_keys($out[0]['deps']) as $i) {
                $ret[] = $i;
            }
            self::_getCache()->save($ret, $cacheId);
        }
        return $ret;
    }
}
