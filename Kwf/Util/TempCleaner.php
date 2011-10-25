<?php
class Kwf_Util_TempCleaner
{
    /**
     * @param integer $cleanOlderThan [optional] Clean files older than x seconds
     */
    public static function clean($cleanOlderThan = 3600)
    {
        if (!is_int($cleanOlderThan) || $cleanOlderThan < 1) {
            throw new Kwf_Exception("First parameter must be of type integer and may not be smaller than 1");
        }

        foreach (new DirectoryIterator('temp') as $f) {
            if ($f->isFile() && $f->getFilename() != '.gitignore' && $f->getMTime() < time() - $cleanOlderThan) {
                unlink($f->getPathname());
            }
        }
    }
}
