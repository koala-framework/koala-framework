<?php
class Vps_Util_Git
{
    private $_path;
    public function __construct($path)
    {
        $this->_path = (string)$path;
        if (!file_exists($path.'/.git')) {
            throw new Vps_Exception("Invalid path '$path', no git wc");
        }
    }

    public static function web()
    {
        static $i;
        if (!isset($i)) $i = new self('.');
        return $i;
    }

    public static function vps()
    {
        static $i;
        if (!isset($i)) $i = new self(VPS_PATH);
        return $i;
    }

    public static function getAuthorName()
    {
        if (isset($_ENV['GIT_AUTHOR_NAME'])) {
            return $_ENV['GIT_AUTHOR_NAME'];
        }
        return trim(`git config user.name`);
    }

    public static function getAuthorEMail()
    {
        if (isset($_ENV['GIT_AUTHOR_EMAIL'])) {
            return $_ENV['GIT_AUTHOR_EMAIL'];
        }
        return trim(`git config user.email`);
    }

    public static function getCommitterName()
    {
        if (isset($_ENV['GIT_COMMITTER_NAME'])) {
            return $_ENV['GIT_COMMITTER_NAME'];
        }
        return trim(`git config user.name`);
    }

    public static function getCommitterEMail()
    {
        if (isset($_ENV['GIT_COMMITER_EMAIL'])) {
            return $_ENV['GIT_COMMITER_EMAIL'];
        }
        return trim(`git config user.email`);
    }

    public static function getAuthorEnvVars()
    {
        return "GIT_AUTHOR_NAME=".escapeshellarg(Vps_Util_Git::getAuthorName()).
               " GIT_AUTHOR_EMAIL=".escapeshellarg(Vps_Util_Git::getAuthorEMail()).
               " GIT_COMMITTER_NAME=".escapeshellarg(Vps_Util_Git::getCommitterName()).
               " GIT_COMMITTER_EMAIL=".escapeshellarg(Vps_Util_Git::getCommitterEMail());
    }

    public static function getGitVersion()
    {
        $cmd = "git --version";
        $gitVersion = trim(exec($cmd));
        if (!preg_match('#^git version ([0-9\\.]+)$#', $gitVersion, $m)) {
            throw new Vps_Exception("can't detect git version");
        }
        return $m[1];
    }

    public function tag($tag, $args = '', $object = '')
    {
        $cmd = "tag -a -m ".escapeshellarg('tagged').' '.$args.' '.escapeshellcmd($tag);
        if ($object) $cmd .= escapeshellcmd($object);
        $this->system($cmd);
        $this->system("push origin tag ".escapeshellcmd($tag));
    }

    public function checkout($target)
    {
        $this->system("checkout ".escapeshellcmd($target));
    }

    public function fetch()
    {
        $this->system("fetch origin");
    }

    public function pull()
    {
        $this->system("pull origin");
    }

    public function checkClean()
    {
        $d = getcwd();
        chdir($this->_path);
        system("git diff --quiet --exit-code", $ret);
        chdir($d);
        if ($ret) {
            throw new Vps_ClientException("You must not have modified files in '$this->_path'");
        }
    }

    public function checkUpdated()
    {
        $this->system("fetch origin");

        $d = getcwd();
        chdir($this->_path);
        system("git diff --quiet --exit-code HEAD..origin/$branch", $ret);
        chdir($d);
        if ($ret) {
            throw new Vps_ClientException("Not up to date '$this->_path'");
        }
    }

    public function getActiveBranch()
    {
        $d = getcwd();
        $cmd = "git branch | grep '^*'";
        chdir($this->_path);
        $ret = exec($cmd);
        chdir($d);
        if (!$ret) return false;
        if (!substr($ret, 0, 2)=='* ') return false;
        return substr($ret, 2);
    }

    public function system($cmd)
    {
        $d = getcwd();
        $cmd = "git ".$cmd;
        chdir($this->_path);
        $ret = system($cmd, $retVal);
        chdir($d);
        if ($retVal) {
            throw new Vps_Exception("Command failed: $cmd");
        }
        return $ret;
    }
}
