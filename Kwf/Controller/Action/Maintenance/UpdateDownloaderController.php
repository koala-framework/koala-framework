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
        $urls = array(
            'library.tar.gz' => $this->_getParam('libraryUrl'),
            'kwf.tar.gz' => $this->_getParam('kwfUrl'),
            'app.tar.gz' => $this->_getParam('appUrl'),
        );
        
        $c = new Kwf_Util_ProgressBar_Adapter_Cache($this->_getParam('progressNum'));
        $progress = new Zend_ProgressBar($c, 0, 6);

        foreach ($urls as $target=>$url) {
            $progress->next(1, 'Downloading '.$target);
            exec("wget -O ".escapeshellarg($target)." ".escapeshellarg($url), $out, $ret);
            if ($ret) {
                throw new Kwf_Exception_Client("Download Failed:".implode("\n", $out));
            }
        }

        foreach ($urls as $target=>$url) {
            $progress->next(1, 'Extracting '.$target);

            $dir = tempnam('.', 'downloader');
            unlink($dir);
            mkdir($dir);
            exec("tar xfz $target -C $dir", $out, $ret);
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
                $files = glob('{,.}*', GLOB_BRACE);
                foreach($files as $file) {
                    if ($file == '.' || $file == '..') continue;
                    if (substr($file, -4) == '-lib' || $file == 'library') continue;
                    rename($file, $backupDir.'/'.$file);
                }

                //move in new files
                $files = glob($dirs[0].'/{,.}*', GLOB_BRACE);
                foreach($files as $file) {
                    if ($file == '.' || $file == '..') continue;
                    rename($dirs[0].'/'.$file, './'.$file);
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
