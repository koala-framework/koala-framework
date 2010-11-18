<?php
class Vps_Update_33020 extends Vps_Update
{
    protected function _systemCheckRet($cmd)
    {
        $ret = null;
        passthru($cmd, $ret);
        if ($ret != 0) throw new Vps_ClientException("Command failed");
    }

    public function update()
    {
        $path = Vps_Registry::get('config')->libraryPath;
        if (file_exists($path.'/.git')) return;

        $cwd = getcwd();

        chdir($path);

        echo "\nconverting $path to git... (dauert a bissi)\n";

        $this->_systemCheckRet("svn up");

        if (trim(`hostname`) == 'vivid') {
            $gitUrl = "ssh://git.vivid-planet.com/git/library";
        } else {
            $gitUrl = "ssh://vivid@git.vivid-planet.com/git/library";
        }

        $cmd = "git clone $gitUrl gitwc";
        echo "$cmd\n";
        $this->_systemCheckRet($cmd);

        chdir("gitwc");
        $cmd = "git checkout latest-svn-trunk";
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

        $cmd = "find . -not -path './vps*' -name .svn | xargs rm -rf";
        echo "$cmd\n";
        $this->_systemCheckRet($cmd);

        $cmd = "git stash";
        echo "$cmd\n";
        $this->_systemCheckRet($cmd);

        $cmd = "git checkout master";
        echo "$cmd\n";
        $this->_systemCheckRet($cmd);

        $cmd = "git stash pop";
        echo "$cmd\n";
        $this->_systemCheckRet($cmd);

        echo "\nsvn R.I.P.\n";

        chdir($cwd);
    }
}
