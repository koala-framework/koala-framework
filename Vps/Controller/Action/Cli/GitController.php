<?php
class Vps_Controller_Action_Cli_GitController extends Vps_Controller_Action_Cli_Abstract
{
    public function checkoutStagingAction()
    {
        if (!file_exists('.git')) $this->_convertToGit();

        Vps_Util_Git::vps()->fetch();
        Vps_Util_Git::web()->fetch();

        Vps_Util_Git::web()->checkout("staging");
        $appId = Vps_Registry::get('config')->application->id;
        Vps_Util_Git::vps()->checkout("$appId-staging");
        exit;
    }

    public function checkoutMasterAction()
    {
        if (!file_exists('.git')) $this->_convertToGit();

        Vps_Util_Git::vps()->fetch();
        Vps_Util_Git::web()->fetch();

        Vps_Util_Git::vps()->checkout(trim(file_get_contents('application/vps_branch')));
        $appId = Vps_Registry::get('config')->application->id;
        Vps_Util_Git::web()->checkout("master");
        exit;
    }

    public function checkoutProductionAction()
    {
        if (!file_exists('.git')) $this->_convertToGit();

        Vps_Util_Git::vps()->fetch();
        Vps_Util_Git::web()->fetch();

        $appId = Vps_Registry::get('config')->application->id;
        Vps_Util_Git::vps()->checkout("$appId-production");
        Vps_Util_Git::web()->checkout("production");

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
        $this->_convertToGit();

        $host = Vps_Registry::get('config')->server->host;
        if ($host == 'vivid' || $host == 'vivid-test-server') {
            echo "Converting ".VPS_PATH."\n";
            chdir(VPS_PATH);
            $this->_convertToGit();
        } else {
            if (!file_exists('vps-lib')) {
                $cmd = "git clone git@github.com:vivid-planet/vps.git vps-lib";
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

                unlink('application/include_path');
            }
        }
    }

    private function _convertWcToGit()
    {
        if (!file_exists('.svn')) {
            echo "is already converted\n";
            return;
        }
        if (!file_exists('.git')) {
            echo "strange, .svn AND .git exist\n";
            return;
        }
        $id = Vps_Registry::get('config')->application->id;
        $gitUrl = "git@github.com:vivid-planet/$id.git";

        $cmd = "git clone $gitUrl gitwc";
        echo "$cmd\n";
        $this->_systemCheckRet($cmd);

        $cmd = "mv gitwc/.git .git";
        echo "$cmd\n";
        $this->_systemCheckRet($cmd);

        $cmd = "rm -rf gitwc";
        echo "$cmd\n";
        $this->_systemCheckRet($cmd);

        $cmd = "svn propget svn:ignore --recursive --xml";
        echo "$cmd\n";
        $xml = new SimpleXMLElement(shell_exec($cmd));
        foreach ($xml->target as $target) {
            $target['path'];
            $svnIgnore = '';
            foreach ($target->property as $property) {
                if ($property['name'] == 'svn:ignore') {
                    $svnIgnore = (string)$property;
                }
            }
            if ($target['path'] == '') $target['path'] = '.';
            if ($target['path'] == '.') $svnIgnore .= "\nvps-lib";
            if (!file_exists($target['path'])) {
                mkdir($target['path']);
                $cmd = 'git add '.$target['path'];
                echo "$cmd\n";
                $this->_systemCheckRet($cmd);
            }
            if (!file_exists($target['path'].'/.gitignore')) {
                echo "writing {$target['path']}/.gitignore\n";
                file_put_contents($target['path'].'/.gitignore', $svnIgnore);
                $cmd = 'git add -f '.$target['path']."/.gitignore";
                echo "$cmd\n";
                $this->_systemCheckRet($cmd);
            }
        }

        $cmd = "find -name .svn | xargs rm -rf";
        echo "$cmd\n";
        $this->_systemCheckRet($cmd);
    }
}
