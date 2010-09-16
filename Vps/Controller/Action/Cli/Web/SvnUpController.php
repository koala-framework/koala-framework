<?php
class Vps_Controller_Action_Cli_Web_SvnUpController extends Vps_Controller_Action_Cli_Abstract
{
    public static function getHelp()
    {
        return "svn update web+vps";
    }

    public static function getHelpOptions()
    {
        return array(
            array(
                'param'=> 'with-library',
                'help' => 'updates library as well'
            ),
            array(
                'param'=> 'skip-update',
                'help' => 'skip update scripts and so don\'t clear caches'
            )
        );
    }

    public function preDispatch()
    {
        if ($this->_getParam('debug')) Vps_Util_Git::setDebugOutput(true);
        parent::preDispatch();
    }

    private function _update($path)
    {
        if (file_exists($path.'/.svn')) {
            passthru('svn up '.$path, $ret);
            if ($ret) throw new Vps_Exception("SVN Update failed");
        } else {
            $git = new Vps_Util_Git($path);
            $git->system("pull --rebase");
        }
    }

    public function indexAction()
    {
        if (file_exists('.svn')) {
            echo "updating web\n";
            $this->_update('.');

            echo "\nupdating vps\n";
            $this->_update(VPS_PATH);

            if ($this->_getParam('with-library')) {
                echo "\nupdating library\n";
                $this->_update(Vps_Registry::get('config')->libraryPath);
            } else {
                echo "\n\033[01;33mlibrary skipped\033[00m: use --with-library if you wish to update library as well\n";
            }

            echo "\n";
            if ($this->_getParam('skip-update')) {
                echo "\n\033[01;33mupdate skipped\033[00m\n";
            } else {
                system("php bootstrap.php update", $ret);
                exit($ret);
            }
            exit;

        } else {
            $this->_forward('update', 'git', 'vps_controller_action_cli');
        }
    }

    public function checkForModifiedFilesAction()
    {
        self::checkForModifiedFiles(false);
        $this->_helper->viewRenderer->setNoRender(true);
    }

    public static function checkForModifiedFiles($checkRemote)
    {
        if (file_exists('.svn')) {
            self::_check('.', $checkRemote);
            echo "Web OK\n";
            self::_check(VPS_PATH, $checkRemote);
            echo "Vps OK\n";
        } else {
            $appId = Vps_Registry::get('config')->application->id;
            Vps_Util_Git::web()->fetch();
            if (Vps_Util_Git::web()->getActiveBranch() == 'production') {
                Vps_Util_Git::web()->checkClean("origin/production");
            } else {
                Vps_Util_Git::web()->checkClean("origin/master");
            }
            echo "Web OK\n";
            Vps_Util_Git::vps()->fetch();
            if (Vps_Util_Git::vps()->getActiveBranch() == 'production/'.$appId) {
                Vps_Util_Git::vps()->checkClean("origin/production/$appId");
            } else {
                Vps_Util_Git::vps()->checkClean("origin/".trim(file_get_contents('application/vps_branch')));
            }
            echo "Vps OK\n";
        }
    }

    private static function _check($path, $checkRemote)
    {
        $cmd = 'svn st --xml ';
        if ($checkRemote) $cmd .= '-u ';
        $cmd .= $path;
        exec($cmd, $out, $ret);
        if ($ret) {
            throw new Vps_ClientException("Failed checking for modified files");
        }
        $xml = new SimpleXMLElement(implode('', $out));
        if (!$xml) {
            throw new Vps_ClientException("Failed checking for modified files");
        }
        $files = array();
        foreach ($xml->target->entry as $e) {
            $files[(string)$e->{'wc-status'}['item']][] = (string)$e['path'];
        }
        if ($files) {
            if (!isset($files['normal']) || count($files) > 1) {
                echo "working copy contains ";
                foreach ($files as $status=>$f) {
                    if ($status == 'normal') continue;
                    echo count($f)." $status ";
                }
                echo "files\n";
            }
            if (isset($files['normal'])) {
                echo "working copy is not up to date\n";
            }
            if ($path == '.') $path = getcwd();
            throw new Vps_ClientException("You must not have modified files in '$path'");
        }
    }
}
