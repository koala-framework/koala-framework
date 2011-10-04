<?php
class Vps_Util_Git
{
    private static $_debug = false;
    private $_path;
    private $_fetched = false;
    public function __construct($path)
    {
        $this->_path = (string)$path;
        if ($this->_path == '.') $this->_path = getcwd();
        if (!file_exists($path.'/.git')) {
            throw new Vps_Exception("Invalid path '$path', no git wc");
        }
    }

    public static function setDebugOutput($enable = true)
    {
        self::$_debug = $enable;
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
        if ($object) $cmd .= ' '.escapeshellcmd($object);
        $this->system($cmd);

        if ($args == '-f') {
            if ($this->revParse("refs/tags/$tag")) {
                $this->system("push origin :refs/tags/$tag"); //tag loeschen
            }
        }
        $this->system("push origin tag ".escapeshellcmd($tag));
    }

    public function branch($branch, $args = '', $object = '')
    {
        $cmd = "branch $args ".escapeshellcmd($branch).' '.escapeshellcmd($object);
        $this->system($cmd);
        $this->system("push origin ".escapeshellcmd($branch));
    }

    public function revParse($obj)
    {
        $d = getcwd();
        chdir($this->_path);
        $cmd = "git rev-parse ".escapeshellcmd($obj)." 2>/dev/null";
        if (self::$_debug) echo $cmd."\n";
        $ret = exec($cmd, $out, $retVar);
        chdir($d);
        if ($retVar) return false;
        if (!$ret) return false;
        return trim($ret);
    }

    public function checkout($target)
    {
        $this->system("checkout ".escapeshellcmd($target));
    }

    public function checkoutBranch($branch, $target, $args = '')
    {
        $this->system("checkout $args -b ".escapeshellcmd($branch).' '.escapeshellcmd($target));
    }

    public function fetch()
    {
        if (!$this->_fetched) {
            $this->system("fetch origin");
            $this->_fetched = true;
        }
    }

    public function pull()
    {
        $this->system("pull origin");
    }

    public function checkClean($against)
    {
        $d = getcwd();
        chdir($this->_path);
        $cmd = "git diff --quiet --exit-code $against";
        if (self::$_debug) echo $cmd."\n";
        system($cmd, $ret);
        chdir($d);
        if (self::$_debug) echo "return $ret\n";
        if ($ret) {
            throw new Vps_ClientException("You must not have modified files in '$this->_path'");
        }
    }

    public function getActiveBranch()
    {
        $d = getcwd();
        $cmd = "git branch | grep '^*'";
        chdir($this->_path);
        if (self::$_debug) echo $cmd."\n";
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
        if (self::$_debug) echo $cmd."\n";
        $ret = system($cmd, $retVal);
        chdir($d);
        if ($retVal) {
            throw new Vps_Exception("Command failed: $cmd");
        }
        return $ret;
    }

    public function exec($cmd, &$output)
    {
        $d = getcwd();
        $cmd = "git ".$cmd;
        chdir($this->_path);
        if (self::$_debug) echo $cmd."\n";
        $ret = exec($cmd, $output, $retVal);
        chdir($d);
        if ($retVal) {
            throw new Vps_Exception("Command failed: $cmd");
        }
        return $ret;
    }

    public function getBranches($args = '')
    {
        return $this->_getBranches($args);
    }

    private function _getBranches($args)
    {
        $d = getcwd();
        $cmd = "git branch $args";
        chdir($this->_path);
        if (self::$_debug) echo $cmd."\n";
        exec($cmd, $ret, $retVal);
        chdir($d);
        if ($retVal) {
            throw new Vps_Exception("Command failed: $cmd");
        }
        foreach ($ret as &$i) {
            $i = trim(trim(trim($i), '*'));
        }
        if (!$ret) $ret = array();
        return $ret;
    }

    public function getBranchesNotMerged($args = '-a')
    {
         return $this->_getBranches($args.' --no-merged');
    }

    public function getBranchesContains($commit, $args = '-a')
    {
        try {
            return $this->_getBranches($args.' --contains '.$commit);
        } catch (Vps_Exception $e) {
            return array();
        }
    }

    public function getActiveBranchContains($commit)
    {
        return in_array($this->getActiveBranch(),  $this->getBranchesContains($commit, ''));
    }

    public function productionBranch($branch, $staging)
    {
        $activeBranch = $this->getActiveBranch();
        if (!in_array($branch, $this->getBranches())) {
            if (in_array('origin/'.$branch, $this->getBranches('-r'))) {
                $this->system("checkout -b $branch origin/$branch");
            } else {
                $this->system("checkout $staging");
                $this->system("checkout -b $branch");
                $data = "\n[branch \"$branch\"]\n";
                $data .= "remote = origin\n";
                $data .= "merge = refs/heads/$branch\n";
                file_put_contents($this->_path.'/.git/config', $data, FILE_APPEND);
            }
        } else {
            $this->system("checkout $branch");
        }
        $this->system("merge --no-ff -m \"merge into production for go-online\" $staging");
        $this->system("push origin $branch:refs/heads/$branch");

        if ($activeBranch) $this->checkout($activeBranch);
    }

    public function isEmptyLog($ref)
    {
        $d = getcwd();
        $cmd = "git --no-pager log $ref";
        chdir($this->_path);
        if (self::$_debug) echo $cmd."\n";
        exec($cmd, $ret, $retVal);
        chdir($d);
        if ($retVal) {
            throw new Vps_Exception("Command failed: $cmd");
        }
        $ret = trim(implode("\n", $ret));
        return empty($ret);
    }
}
