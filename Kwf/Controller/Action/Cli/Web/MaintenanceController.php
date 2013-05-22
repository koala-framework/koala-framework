<?php
class Kwf_Controller_Action_Cli_Web_MaintenanceController extends Kwf_Controller_Action_Cli_Abstract
{
    public static function getHelp()
    {
        return 'maintenance (interal)';
    }

    public function clearCacheAction()
    {
        $options = array();
        if ($this->_getParam('skip-other-servers')) {
            $options['skipOtherServers'] = true;
        }

        $c = new Kwf_Util_ProgressBar_Adapter_Cache($this->_getParam('progressNum'));
        $options['progressAdapter'] = $c;
        Kwf_Util_ClearCache::getInstance()->clearCache($this->_getParam('type'), false, true, $options);
        $out = array(
            'success' => true
        );
        echo json_encode($out);
        exit;
    }

    public function updateAction()
    {
        $doneNames = Kwf_Util_Update_Helper::getExecutedUpdatesNames();
        $updates = Kwf_Util_Update_Helper::getUpdates(0, 9999999);
        $data = array();
        $id = 0;
        foreach ($updates as $k=>$u) {
            if (in_array($u->getUniqueName(), $doneNames)) {
                unset($updates[$k]);
            }
        }

        $c = new Kwf_Util_ProgressBar_Adapter_Cache($this->_getParam('progressNum'));

        $runner = new Kwf_Util_Update_Runner($updates);
        $progress = new Zend_ProgressBar($c, 0, $runner->getProgressSteps());
        $runner->setProgressBar($progress);
        if (!$runner->checkUpdatesSettings()) {
            throw new Kwf_Exception_Client("checkSettings failed, update stopped");
        }
        $doneNames = array_merge($doneNames, $runner->executeUpdates());
        $runner->writeExecutedUpdates($doneNames);
 
        $errMsg = '';
        $errors = $runner->getErrors();
        if ($errors) {
            $errMsg .= count($errors)." setup script(s) failed:\n";
            foreach ($errors as $error) {
                $errMsg .= $error['name'].": \n";
                $errMsg .= $error['message']."\n\n";
            }
        }
        
        $message = 'Executed '.count($updates)." update scripts";

        $out = array(
            'success' => true,
            'errMsg' => $errMsg,
            'message' => $message
        );
        echo json_encode($out);
        exit;
    }

    public function downloadUpdatesAction()
    {
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


        $out = array(
            'success' => true
        );
        echo json_encode($out);
        exit;    
    }
}
