<?php
class Vps_Test_Uploads_Model extends Vps_Uploads_Model
{
    public function __construct($config = array())
    {
        $config['proxyModel'] = new Vps_Model_FnF(array(
                'columns' => array('id', 'filename', 'extension', 'mime_type'),
                'data'=> array(
                )
            ));
        $dir = tempnam('/tmp', 'uploadstest');
        unlink($dir);
        mkdir($dir);
        $this->setUploadDir($dir);
        parent::__construct($config);
    }

    public function __destruct()
    {
        $dir = $this->getUploadDir();
        if (substr($dir, 0, 4)!='/tmp') {
            throw new Vps_Exception("dir does not start with /tmp, something is wrong");
        }

        $iterator = new RecursiveDirectoryIterator($dir, FilesystemIterator::SKIP_DOTS);
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
