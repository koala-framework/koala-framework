<?php
class Vps_Controller_Action_Cli_TagController extends Vps_Controller_Action_Cli_Abstract
{
    private static $_svnBase = 'file:///var/lib/svn';

    public static function getHelp()
    {
        if (Vps_Registry::get('config')->server->host != 'vivid') return null;
        return "create new vps-tag";
    }
    public static function getHelpOptions()
    {
        $ret = array();

        $dir = self::_getCurrentBranchDir(VPS_PATH);
        if (preg_match('#^branches/vps/([^/]+)#', $dir, $m)) {
            $branchVersion = $m[1];
        } else if ($dir == 'trunk/vps') {
            $branchVersion = false;
        } else {
            $branchVersion = false;
        }
        $versions = array();
        if ($branchVersion) {
            $maxVersion = $branchVersion;
            foreach (self::_getSvnDirs("tags/vps") as $v) {
                if ($branchVersion && version_compare($branchVersion, $v) == 1) {
                    continue;
                }
                if (!$maxVersion || version_compare($maxVersion, $v) == -1) {
                    $maxVersion = $v;
                }
            }
            if (preg_match('#^([0-9]+)\\.([0-9]+)\\.([0-9]+)-?([0-9]*)$#', $maxVersion, $m)) {
                if (isset($m[4])) {
                    $versions[] = "$m[1].$m[2].$m[3]-".($m[4]+1);
                }
                $versions[] = "$m[1].$m[2].".($m[3]+1);
                //$versions[] = "$m[1].".($m[2]+1).".0";
                //$versions[] = ($m[1]+1).".0.0";
            } else {
                $versions[] = $maxVersion.".0";
            }
        } else {
            $maxVersion = 0;
            foreach (self::_getSvnDirs("tags/vps") as $v) {
                if (substr($v, 0, 6) == 'trunk.') {
                    $maxVersion = max($maxVersion, substr($v, 6));
                }
            }
            $versions[] = "trunk.".($maxVersion+1);
        }


        $ret['vpsVersion'] = array(
            'param'=> 'vps-version',
            'value' => $versions,
            'help' => 'new version-number',
            'allowBlank' => true
        );
        if ($project = self::getProjectName()) {
            $versions = array();
            $maxVersion = false;
            foreach (self::_getSvnDirs("tags/$project") as $v) {
                if (!$maxVersion || version_compare($maxVersion, $v) == -1) {
                    $maxVersion = $v;
                }
            }
            if (preg_match('#^([0-9]+)\\.([0-9]+)\\.([0-9]+)-?([0-9]*)$#', $maxVersion, $m)) {
                if (isset($m[4])) {
                    $versions[] = "$m[1].$m[2].$m[3]-".($m[4]+1);
                }
                $versions[] = "$m[1].$m[2].".($m[3]+1);
                $versions[] = "$m[1].".($m[2]+1).".0";
                $versions[] = ($m[1]+1).".0.0";
            } else {
                if ($maxVersion) {
                    $versions[] = $maxVersion;
                } else {
                    $versions[] = '1.0.0';
                }
            }
            $ret['webVersion'] = array(
                'param'=> 'web-version',
                'value' => $versions,
                'help' => 'new version-number',
                'allowBlank' => true
            );
        }

        return $ret;
    }

    private static function _getSvnDirs($dir)
    {
        $ret = array();
        $b = self::$_svnBase;
        $parentDir = substr($dir, 0, strrpos($dir, '/'));
        $dirs = explode("\n", `svn ls $b/$parentDir`);
        if (!in_array(substr($dir, strrpos($dir, '/')+1).'/', $dirs)) return $ret;
        foreach (explode("\n", `svn ls $b/$dir`) as $i) {
            if ($i) {
                $ret[] = trim($i, '/');
            }
        }
        return $ret;
    }

    private static function _getCurrentBranchDir($dir)
    {
        try {
            $cmd = "cd $dir && svn info --xml";
            $info = new SimpleXMLElement(`$cmd`);
            if (!preg_match('#/((trunk|tags|branches).*)$#', (string)$info->entry->url, $m)) {
                throw new Vps_Exception("Can't detect current branch");
            }
        } catch (Exception $e) {
            throw new Vps_Exception("Can't detect current branch: $cmd");
        }
        return $m[1];
    }

    public function indexAction()
    {
        if (!$this->_getParam('web-version') && !$this->_getParam('vps-version')) {
            throw new Vps_ClientException("You must specify one version parameter");
        }

        $version = $this->_getParam('vps-version');
        if ($version) {
            self::createVpsTag($version);
        }

        $version = $this->_getParam('web-version');
        if ($version) {
            self::createWebTag($version);
        }
        $this->_helper->viewRenderer->setNoRender(true);
    }

    public static function createWebTag($version)
    {
        $project = self::getProjectName();
        if (in_array($version, self::_getSvnDirs("tags/$project"))) {
            echo "$project Version $version exists already\n";
            return;
        }

        $branch = self::_getCurrentBranchDir('.');

        $dir = tempnam('/tmp', 'webtag');
        unlink($dir);
        passthru("svn co --non-recursive ".self::$_svnBase."/$branch/application $dir >/dev/null");
        if (!file_exists($dir.'/config.ini')) {
            throw new Vps_ClientException("Can't change web-version, config.ini not found");
        }
        $c = file_get_contents($dir.'/config.ini');
        $c = preg_replace("#application.version = [^\n]+#", "application.version = $version", $c);
        file_put_contents($dir.'/config.ini', $c);
        passthru("svn ci $dir/config.ini -m \"version++\" >/dev/null");
        passthru("rm -rf $dir >/dev/null");
        self::_createTag($branch, $version, $project);
    }

    public static function getProjectName()
    {
        if (preg_match("#trunk/vps-projekte/(.*)\n#", `svn info`, $m)) {
            return $m[1];
        } else if (preg_match("#branches/(.*)\n#", `svn info`, $m)) {
            return $m[1];
        } else if (preg_match("#trunk/vw-projekte/(.*)\n#", `svn info`, $m)) {
            return $m[1];
        }
        return false;
    }
    public static function createVpsTag($version)
    {
        if (in_array($version, self::_getSvnDirs("tags/vps"))) {
            echo "Vps Version $version exists already\n";
            return;
        }

        $branch = self::_getCurrentBranchDir(VPS_PATH);
        self::_createTag($branch, $version, 'vps');
    }

    private static function _createTag($branch, $version, $project)
    {
        $b = self::$_svnBase;

        if (!preg_match('#^[0-9]+\.[0-9]+\.[0-9]+-?[0-9]*$#', $version) &&
            !preg_match('#^trunk\.[0-9]+$#', $version)
        ) {
            throw new Vps_ClientException("Invalid version number: '$version'");
        }

        passthru("svn mkdir $b/tags/$project -m \"created tags directory\"  2>&1 > /dev/null");

        $versions = self::_getSvnDirs("tags/$project");
        if (in_array($version, $versions)) {
            throw new Vps_ClientException("Tag '$version' exists allready");
        }

        passthru("svn cp $b/$branch $b/tags/$project/$version -m \"created new version through vps-cli\"", $ret);
        if (!$ret) {
            echo "Tag tags/$project/$version from $branch successfully created.\n";
        } else {
            throw new Vps_ClientException("Failed creating Tag '$version'");
        }
    }
}
