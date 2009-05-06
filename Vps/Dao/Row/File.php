<?php
class Vps_Dao_Row_File extends Vps_Db_Table_Row_Abstract
{
    //hilfsfkt wird vor erstellen des caches aufgerufen damit die ordner korrekt
    //erstellt werden. passt nicht wirklich hier her.
    public static function prepareCacheTarget($target)
    {
        $uploadDir = Vps_Dao_Row_File::getUploadDir();
        if (!is_dir($uploadDir . '/cache')) {
            mkdir($uploadDir . '/cache');
        }
        if (!is_dir(dirname($target))) {
            mkdir(dirname($target));
        }
    }

    public function getFileSource()
    {
        //TODO
    }

    public function getFileSize()
    {
        //TODO
    }

    private function _recursiveRemoveDirectory($dir)
    {
        if (!file_exists($dir) || !is_dir($dir)) return;
        $iterator = new RecursiveDirectoryIterator($dir);
        foreach (new RecursiveIteratorIterator($iterator, RecursiveIteratorIterator::CHILD_FIRST) as $file) {
            if ($file->isDir()) {
                rmdir($file->getPathname());
            } else {
                unlink($file->getPathname());
            }
        }
        rmdir($dir);
    }

}
