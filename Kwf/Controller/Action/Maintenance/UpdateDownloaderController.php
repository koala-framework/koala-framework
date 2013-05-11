<?php
class Kwf_Controller_Action_Maintenance_UpdateDownloaderController extends Kwf_Controller_Action
{
    public function indexAction()
    {
        $this->view->assetsType = 'Kwf_Controller_Action_Maintenance:UpdateDownloader';
        $this->view->xtype = 'kwf.maintenance.updateDownloader';
    }

    public function jsonDownloadUpdatesAction()
    {
        if (!is_writable('.')) {
            throw new Kwf_Exception_Client("Root directory is not writeable for ".`whoami`." user");
        }

        $errors = array();
        $files = glob('{,.}*', GLOB_BRACE);
        foreach ($files as $f) {
            if ($f == '..') continue;
            if (!is_writable($f)) {
                $errors[] = $f;
            }
        }
        if ($errors) {
            throw new Kwf_Exception_Client("Following directories are not writeable for ".`whoami`." user<br />".implode("<br />", $errors));
        }

        $urls = array();
        if ($this->_getParam('libraryUrl')) {
            $urls['library.tar.gz'] = $this->_getParam('libraryUrl');
        }
        $urls['kwf.tar.gz'] = $this->_getParam('kwfUrl');
        $urls['app.tar.gz'] = $this->_getParam('appUrl');
        
        $c = new Kwf_Util_ProgressBar_Adapter_Cache($this->_getParam('progressNum'));
        $progress = new Zend_ProgressBar($c, 0, 6);

        foreach ($urls as $target=>$url) {
            $progress->next(1, 'Downloading '.$target);
            exec("wget -O ".escapeshellarg($target)." ".escapeshellarg($url)." 2>&1", $out, $ret);
            if ($ret) {
                throw new Kwf_Exception_Client("Download Failed:".implode("\n", $out));
            }
        }

        foreach ($urls as $target=>$url) {
            $progress->next(1, 'Extracting '.$target);

            $dir = tempnam('.', 'downloader');
            unlink($dir);
            mkdir($dir);
            exec("tar xfz $target -C $dir"." 2>&1", $out, $ret);
            if ($ret) {
                throw new Kwf_Exception_Client("Extraction failed");
            }
            $dirs = glob("$dir/*");
            if (count($dirs) != 1) {
                throw new Kwf_Exception_Client("more than one directory extracted");
            }
            if (!is_dir($dirs[0])) {
                throw new Kwf_Exception_Client("no directory extracted");
            }
            if ($target == 'app.tar.gz') {
                //move away current files to backup
                $backupDir = uniqid("backup").'app';
                mkdir($backupDir);
                $it = new DirectoryIterator(getcwd());
                foreach($it as $file) {
                    if($file->isDot()) continue;
                    if (substr($file->getFilename(), -4) == '-lib' || $file->getFilename() == 'library') continue;
                    if (substr($file->getFilename(), 0, 6) == 'backup') continue;
                    if ($file->getFilename() == 'config.local.ini') continue;
                    if ($file->getFilename() == $backupDir) continue;
                    if ($file->getPathname() == $dir) continue;
                    if ($file->getFilename() == 'app.tar.gz') continue;
                    rename($file->getPathname(), $backupDir.'/'.$file->getFilename());
                }

                //move in new files
                $it = new DirectoryIterator($dirs[0]);
                foreach ($it as $file) {
                    if($file->isDot()) continue;
                    rename($file->getPathname(), './'.$file->getFilename());
                }
                rmdir("$dirs[0]");

            } else {
                $targetDir = false;
                if ($target == 'library.tar.gz') $targetDir = 'library';
                else if ($target == 'kwf.tar.gz') $targetDir = 'kwf-lib';
                rename($targetDir, uniqid("backup").$targetDir);
                rename("$dirs[0]",  $targetDir);
            }
            rmdir($dir);
            unlink($target);
        }
    }
}
