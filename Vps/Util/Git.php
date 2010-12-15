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
        $cmd = "git ".$cmd;
        $cmd = "git branch | grep '^*'";
        chdir($this->_path);
        $ret = system($cmd, $retVal);
        chdir($d);
        if ($retVal) {
            throw new Vps_Exception("Command failed: $cmd");
        }
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
