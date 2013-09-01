<?php
class Kwf_Controller_Action_Maintenance_UpdateDownloaderController extends Kwf_Controller_Action
{
    public function indexAction()
    {
        $this->view->assetsType = 'Kwf_Controller_Action_Maintenance:UpdateDownloader';
        $this->view->xtype = 'kwf.maintenance.updateDownloader';

        $this->view->defaultLibraryUrl = 'https://github.com/vivid-planet/library/archive/master.tar.gz';

        if (Kwf_Config::getValue('updateDownloader.app.github.repository')) {
            $branch = Kwf_Config::getValue('updateDownloader.app.github.branch');
            if (!$branch) $branch = 'master';
            $ghPath = Kwf_Config::getValue('updateDownloader.app.github.user').'/'.
                      Kwf_Config::getValue('updateDownloader.app.github.repository');
            $kwfBranch = trim(file_get_contents('https://raw.github.com/'.$ghPath.'/'.$branch.'/kwf_branch'));
            $this->view->defaultAppUrl = "https://github.com/$ghPath/archive/$branch.tar.gz";
        } else if (!file_exists('kwf_branch')) {
            $kwfBranch = trim(file_get_contents('kwf_branch'));
        } else {
            $kwfBranch = 'master';
        }

        if (Kwf_Config::getValue('updateDownloader.kwf.github.repository')) {
            $ghPath = Kwf_Config::getValue('updateDownloader.kwf.github.user').'/'.
                      Kwf_Config::getValue('updateDownloader.kwf.github.repository');
            $this->view->defaultKwfUrl = "https://github.com/$ghPath/archive/$kwfBranch.tar.gz";
        } else {
            $this->view->defaultKwfUrl = 'https://github.com/vivid-planet/koala-framework/archive/'.$kwfBranch.'.tar.gz';
        }
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
            throw new Kwf_Exception_Client("Following directories are not writeable for ".`whoami`." user:<br />".implode("<br />", $errors));
        }

        if (Kwf_Config::getValue('server.phpCli')) {

            $cmd = Kwf_Config::getValue('server.phpCli')." bootstrap.php maintenance download-updates ";
            $cmd .= " --progressNum=".escapeshellarg($this->_getParam('progressNum'));
            if ($this->_getParam('libraryUrl')) {
                $cmd .= " --libraryUrl=".escapeshellarg($this->_getParam('libraryUrl'));
            }
            $cmd .= " --kwfUrl=".escapeshellarg($this->_getParam('kwfUrl'));
            $cmd .= " --appUrl=".escapeshellarg($this->_getParam('appUrl'));
            $procData = Kwf_Util_BackgroundProcess::start($cmd, $this->view);
            $this->view->assign($procData);
        } else {
            self::downloadUpdates($this->getRequest(), $this->view);
        }
    }

    public static function downloadUpdates($request, $view)
    {
        $urls = array();
        if ($request->getParam('libraryUrl')) {
            $urls['library.tar.gz'] = $request->getParam('libraryUrl');
        }
        $urls['kwf.tar.gz'] = $request->getParam('kwfUrl');
        $urls['app.tar.gz'] = $request->getParam('appUrl');

        $c = new Kwf_Util_ProgressBar_Adapter_Cache($request->getParam('progressNum'));
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
                    if ($file->getFilename() == 'config_section') continue;
                    if ($file->getFilename() == 'php.ini') continue;  //php config might be in document_root (wtf, godaddy)
                    if ($file->getFilename() == 'php5.ini') continue; //php config might be in document_root (wtf, godaddy)
                    if ($file->getFilename() == $backupDir) continue;
                    if ($file->getPathname() == $dir) continue;
                    if ($file->getFilename() == 'app.tar.gz') continue;
                    if ($file->getFilename() == 'temp') continue;
                    if ($file->getFilename() == 'log') continue;
                    rename($file->getPathname(), $backupDir.'/'.$file->getFilename());
                }

                //move in new files
                $it = new DirectoryIterator($dirs[0]);
                foreach ($it as $file) {
                    if($file->isDot()) continue;
                    if ($file->getFilename() == 'temp') continue;
                    if ($file->getFilename() == 'log') continue;
                    rename($file->getPathname(), './'.$file->getFilename());
                }
                system("rm -r ".escapeshellarg($dirs[0]));

                $oldHtaccess = file_get_contents($backupDir.'/.htaccess');
                if (!strpos('php_flag magic_quotes_gpc off', $oldHtaccess)) {
                    $c = file_get_contents('.htaccess');
                    $c = str_replace('php_flag magic_quotes_gpc off', '', $c);
                    file_put_contents('.htaccess', $c);
                }

            } else {
                $targetDir = false;
                if ($target == 'library.tar.gz') {
                    $targetDir = 'library';
                } else if ($target == 'kwf.tar.gz') {
                    $targetDir = 'kwf-lib';
                    copy($targetDir.'/include_path', $dirs[0].'/include_path');
                }
                rename($targetDir, uniqid("backup").$targetDir);
                rename("$dirs[0]",  $targetDir);
            }
            rmdir($dir);
            unlink($target);
        }

        Kwf_Util_ClearCache::getInstance()->clearCache('all', false, true);
    }

    public function jsonExecuteUpdatesAction()
    {

        if (Kwf_Config::getValue('server.phpCli')) {
            $cmd = Kwf_Config::getValue('server.phpCli')." bootstrap.php maintenance update ";
            $cmd .= " --progressNum=".escapeshellarg($this->_getParam('progressNum'));
            $procData = Kwf_Util_BackgroundProcess::start($cmd, $this->view);
            $this->view->assign($procData);
        } else {
            Kwf_Controller_Action_Maintenance_UpdateController::executeUpdates($this->getRequest(), $this->view);
        }
    }
}
