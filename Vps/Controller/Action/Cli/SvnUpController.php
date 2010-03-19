<?php
class Vps_Controller_Action_Cli_SvnUpController extends Vps_Controller_Action_Cli_Abstract
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

    private function _update($path)
    {
        passthru('svn up '.$path, $ret);
        if ($ret) throw new Vps_Exception("SVN Update failed");
    }

    public function indexAction()
    {
        $doUpdate = true;
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
        } else {
            Vps_Util_Git::web()->fetch();
            if (Vps_Util_Git::web()->getActiveBranch() == 'master') {
                Vps_Util_Git::web()->system("rebase origin/master");
            } else {
                echo "web: {Vps_Util_Git::web()->getActiveBranch()} != master, daher wird kein autom. rebase ausgefuehrt.\n";
                $doUpdate = false;
            }

            if (file_exists('application/include_path')) {
                $vp = str_replace('%vps_branch%', trim(file_get_contents('application/vps_branch')), trim(file_get_contents('application/include_path')));
            } else {
                $vp = getcwd().'/vps-lib';
            }
            $g = new Vps_Util_Git($vp);
            $g->fetch();
            $vpsBranch = trim(file_get_contents('application/vps_branch'));
            if ($g->getActiveBranch() == $vpsBranch) {
                Vps_Util_Git::web()->system("rebase origin/$vpsBranch");
            } else {
                echo "vps: {$g->getActiveBranch()} != $vpsBranch, daher wird kein autom. rebase ausgefuehrt.\n";
                $doUpdate = false;
            }
        }

        /* TODO GIT
        $projectIds = array();
        if (Vps_Registry::get('config')->todo->projectIds) {
            $projectIds = Vps_Registry::get('config')->todo->projectIds->toArray();
        }
        if ($projectIds && Vps_Registry::get('config')->todo->markAsOnTestOnUpdate) {
            $m = Vps_Model_Abstract::getInstance('Vps_Util_Model_Todo');
            $s = $m->select()
                    ->whereEquals('project_id', $projectIds)
                    ->whereEquals('status', 'committed');
            $doneTodos = $m->getRows($s);
            foreach ($doneTodos as $todo) {
                if (!$todo->done_revision) continue;
                if ($this->_hasRevisionInHistory('.', $todo->done_revision)
                    || $this->_hasRevisionInHistory(VPS_PATH, $todo->done_revision)
                ) {
                    $todo->status = 'test';
                    $todo->test_date = date('Y-m-d');
                    $todo->save();
                    echo "\ntodo #{$todo->id} ({$todo->title}) als auf test markiert";
                }
            }
        }
        */

        echo "\n";
        if ($this->_getParam('skip-update')) {
            echo "\n\033[01;33mupdate skipped\033[00m\n";
        } else {
            if ($doUpdate) {
                system("php bootstrap.php update", $ret);
                exit($ret);
            } else {
                echo "Updates wurden NICHT ausgefuehrt.\n";
            }
        }

        exit;
    }
    private function _hasRevisionInHistory($path, $revision)
    {
        if (file_exists($path.'/.svn')) {
            $log = `svn log --xml --revision $revision $path`;
            $log = new SimpleXMLElement($log);
            if (count($log->logentry)) return true;
        } else {
            //NOT YET IMPLEMENTED
            return false;
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
            Vps_Util_Git::web()->checkClean();
            Vps_Util_Git::web()->checkUpdated();
            echo "Web OK\n";
            Vps_Util_Git::vps()->checkClean();
            Vps_Util_Git::vps()->checkUpdated();
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
