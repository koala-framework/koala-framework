<?php
class Vps_Controller_Action_Cli_TagController extends Vps_Controller_Action_Cli_Abstract
{
    private static $_svnBase = 'file:///var/lib/svn';

    public static function getHelp()
    {
        return "create new vps-tag";
    }
    public static function getHelpOptions()
    {
        $ret = array();

        $values = array('trunk/vps');
        foreach (self::_getSvnDirs("branches/vps") as $b) {
            $values[] = 'branches/vps/'.$b;
        }
        $ret[] = array(
            'param' => 'vps-branch',
            'value' => $values,
            'valueOptional'=>true
        );

        $ret[] = array(
            'param'=> 'vps-version',
            'value' => self::_getNextVersions("tags/vps"),
            'help' => 'new version-number',
            'allowBlank' => true
        );
        if (preg_match("#trunk/vps-projekte/(.*)\n#", `svn info`, $m)) {
            $values = array("trunk/vps-projekte/$m[1]");
            foreach (self::_getSvnDirs("branches/$m[1]") as $b) {
                $values[] = 'branches/'.$m[1].'/'.$b;
            }
            $ret[] = array(
                'param' => 'web-branch',
                'value' => $values,
                'valueOptional'=>true
            );
            $ret[] = array(
                'param'=> 'web-version',
                'value' => self::_getNextVersions("tags/$m[1]"),
                'help' => 'new version-number',
                'allowBlank' => true
            );
        }

        return $ret;
    }

    private static function _getNextVersions($dir)
    {
        $ret = array();
        $maxVersion = false;
        foreach (self::_getSvnDirs($dir) as $v) {
            if (!$maxVersion || version_compare($maxVersion, $v)) {
                $maxVersion = $v;
            }
        }
        if (preg_match('#^([0-9]+)\\.([0-9]+)\\.([0-9]+)-?([0-9]*)$#', $maxVersion, $m)) {
            if (isset($m[4])) {
                $ret[] = "$m[1].$m[2].$m[3]-".($m[4]+1);
            }
            $ret[] = "$m[1].$m[2].".($m[3]+1);
            $ret[] = "$m[1].".($m[2]+1).".0";
            $ret[] = ($m[1]+1).".0.0";
        } else {
            $ret = $maxVersion;
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

    public function indexAction()
    {
        if (!$this->_getParam('web-version') && !$this->_getParam('vps-version')) {
            throw new Vps_ClientException("You must specify one version parameter");
        }

        $branch = $this->_getParam('vps-branch');
        $version = $this->_getParam('vps-version');
        if ($version) {
            $this->_createTag($branch, $version, 'vps');
        }

        $branch = $this->_getParam('web-branch');
        $version = $this->_getParam('web-version');
        if ($version) {
            preg_match("#trunk/vps-projekte/(.*)\n#", `svn info`, $m);
            $this->_createTag($branch, $version, $m[1]);
        }


        exit();
    }

    private function _createTag($branch, $version, $project)
    {
        if (!preg_match('#^[0-9]+\.[0-9]+\.[0-9]+-?[0-9]*$#', $version)) {
            throw new Vps_ClientException("Invalid version number: '$version'");
        }
        $versions = self::_getSvnDirs("tags/$project");
        if (in_array($version, $versions)) {
            throw new Vps_ClientException("Tag '$version' exists allready");
        }

        $b = self::$_svnBase;
        passthru("svn cp $b/$branch $b/tags/$project/$version -m \"created new version through vps-cli\"", $ret);
        if (!$ret) {
            echo "Tag tags/$project/$version from $branch successfully created.\n";
        }
    }
}
