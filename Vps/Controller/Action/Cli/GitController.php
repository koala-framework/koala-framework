<?php
class Vps_Controller_Action_Cli_GitController extends Vps_Controller_Action_Cli_Abstract
{
    public static function getHelp()
    {
        return 'various git helpers';
    }

    public function preDispatch()
    {
        if ($this->_getParam('debug')) Vps_Util_Git::setDebugOutput(true);
        parent::preDispatch();
    }

    private function _eventuallyConvertToGitAndRestart()
    {
        if (!file_exists('.git')) {
            $this->_convertToGit();

            //nach git konvertierung script nochmal neu starten, da der VPS_PATH sich geändert haben kann
            $argv = $_SERVER['argv'];
            unset($argv[0]);
            passthru('php bootstrap.php '.implode(' ', $argv));
            exit;
        }
    }

    public function checkoutStagingAction()
    {
        $this->_eventuallyConvertToGitAndRestart();

        Vps_Util_Git::vps()->fetch();
        Vps_Util_Git::web()->fetch();

        Vps_Util_Git::web()->checkout("staging");
        $appId = Vps_Registry::get('config')->application->id;
        Vps_Util_Git::vps()->checkout("$appId-staging");
        exit;
    }

    public function checkoutMasterAction()
    {
        $this->_eventuallyConvertToGitAndRestart();

        Vps_Util_Git::vps()->fetch();
        Vps_Util_Git::web()->fetch();

        Vps_Util_Git::vps()->checkout(trim(file_get_contents('application/vps_branch')));
        $appId = Vps_Registry::get('config')->application->id;
        Vps_Util_Git::web()->checkout("master");
        exit;
    }

    public function checkoutProductionAction()
    {
        $this->_eventuallyConvertToGitAndRestart();

        Vps_Util_Git::vps()->fetch();
        Vps_Util_Git::web()->fetch();

        //die werden von go-online raufkopiert
        Vps_Util_Git::vps()->system("checkout ".escapeshellarg('Vps/Util/Git.php'));
        Vps_Util_Git::vps()->system("checkout ".escapeshellarg('Vps/Controller/Action/Cli/GitController.php'));
        $appId = Vps_Registry::get('config')->application->id;
        if (!Vps_Util_Git::vps()->revParse("production-$appId")) {
            Vps_Util_Git::vps()->branch("production-$appId", '', "origin/production/$appId");
        }
        if (Vps_Util_Git::vps()->getActiveBranch() != "production-$appId") {
            Vps_Util_Git::vps()->checkout("production-$appId");
        }
        Vps_Util_Git::vps()->system("rebase origin/production/$appId");

        if (!Vps_Util_Git::web()->revParse("production")) {
            Vps_Util_Git::web()->branch("production", '', "origin/production");
        }
        if (Vps_Util_Git::web()->getActiveBranch() != "production") {
            Vps_Util_Git::web()->checkout("production");
        }
        Vps_Util_Git::web()->system("rebase origin/production");

        system("php bootstrap.php update", $ret);
        exit($ret);
    }

    public function convertToGitAction()
    {
        $this->_convertToGit();
        exit;
    }

    private function _convertToGit()
    {
        echo "Converting ".getcwd()." to git\n";
        $this->_convertWcToGit(Vps_Registry::get('config')->application->id);

        $host = Vps_Registry::get('config')->server->host;
        if ($host == 'vivid' && Vps_Setup::getConfigSection()!='vivid') {
            echo "Converting ".VPS_PATH."\n";
            $branch = file_get_contents('application/vps_branch');
            chdir(VPS_PATH);
            $this->_convertWcToGit('vps', $branch);
        } else {
            if (!file_exists('vps-lib')) {
                if (`hostname` == 'vivid') {
                    $gitUrl = "ssh://git.vivid-planet.com/git/vps";
                } else {
                    $gitUrl = "ssh://vivid@git.vivid-planet.com/git/vps";
                }
                $cmd = "git clone $gitUrl vps-lib";
                echo "$cmd\n";
                $this->_systemCheckRet($cmd);

                $branch = file_get_contents('application/vps_branch');

                chdir('vps-lib');

                $cmd = "git branch --track $branch origin/$branch";
                echo "$cmd\n";
                $this->_systemCheckRet($cmd);

                $cmd = "git checkout ".$branch;
                echo "$cmd\n";
                $this->_systemCheckRet($cmd);

                chdir('..');

                copy(VPS_PATH.'/include_path', 'vps-lib/include_path');

                unlink('application/include_path');
            }
        }
    }

    private function _convertWcToGit($id, $branch = null)
    {
        if (!file_exists('.svn')) {
            echo "is already converted\n";
            return;
        }
        if (file_exists('.git')) {
            echo "strange, .svn AND .git exist\n";
            return;
        }
        $this->_systemCheckRet("svn up");

        if (!$branch) {
            $branch = 'master';
            $xml = simplexml_load_string(`svn info --xml`);
            if (preg_match('#branches/[^/]+/([^/]+)$#', (string)$xml->entry->url, $m)) {
                $branch = $m[1];
            }
        }
        if ($branch == 'trunk') $branch = 'master';

        if (`hostname` == 'vivid') {
            $gitUrl = "ssh://git.vivid-planet.com/git/$id";
        } else {
            $gitUrl = "ssh://vivid@git.vivid-planet.com/git/$id";
        }

        $cmd = "git clone $gitUrl gitwc";
        echo "$cmd\n";
        $this->_systemCheckRet($cmd);

        chdir("gitwc");
        if ($branch && $branch != 'master') {
            $cmd = "git branch --track $branch origin/$branch";
            echo "$cmd\n";
            $this->_systemCheckRet($cmd);
        }

        $cmd = "git checkout latest-svn-".($branch=='master' ? 'trunk' : $branch);
        echo "$cmd\n";
        $this->_systemCheckRet($cmd);

        chdir("..");

        $cmd = "mv gitwc/.git .git";
        echo "$cmd\n";
        $this->_systemCheckRet($cmd);

        chdir("gitwc");
        $cmd = "find -name .gitignore | xargs -t -I xxx mv xxx ../xxx";
        echo "$cmd\n";
        $this->_systemCheckRet($cmd);
        chdir("..");

        $cmd = "rm -rf gitwc";
        echo "$cmd\n";
        $this->_systemCheckRet($cmd);

        $cmd = "find -name .svn | xargs rm -rf";
        echo "$cmd\n";
        $this->_systemCheckRet($cmd);

        $cmd = "git stash";
        echo "$cmd\n";
        $this->_systemCheckRet($cmd);

        $cmd = "git checkout ".$branch;
        echo "$cmd\n";
        $this->_systemCheckRet($cmd);

        $cmd = "git stash pop";
        echo "$cmd\n";
        $this->_systemCheckRet($cmd);
    }
}
