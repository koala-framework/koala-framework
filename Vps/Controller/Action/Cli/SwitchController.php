<?php
class Vps_Controller_Action_Cli_SwitchController extends Vps_Controller_Action_Cli_Abstract
{
    public static function getHelp()
    {
        return "switch web and vps working copy";
    }
    public static function getHelpOptions()
    {
        $value = array('trunk');
        $value = array_merge($value, self::_getConfigSectionsWithHost());
        return array(
            array(
                'param'=> 'version',
                'value'=> $value,
                'valueOptional' => true,
            )
        );
    }
    private function _getIncludePathBase()
    {
        $path = trim(file_get_contents('application/include_path'));
        if (substr($path, -12) == '%vps_branch%') {
            return substr($path, 0, -12);
        } else if (substr($path, -18) == '%vps_branch%-clean') {
            return substr($path, 0, -18);
        } else if (substr($path, -3) == 'tag') {
            return substr($path, 0, -3);
        } else {
            if (preg_match('#^(.*)[0-9]+\\.[0-9]+(\\.[0-9]+(-[0-9]+)?)?$#', $path, $m)) {
                return $m[1];
            } else {
                return false;
            }
        }
    }

    public function indexAction()
    {
        $webVersion = $this->_getParam('version');
        if (!$webVersion) throw new Vps_ClientException("--version is required");
        $configSections = self::_getConfigSectionsWithHost();
        $vpsVersion = null;
        if (in_array($webVersion, $configSections)) {
            $config = Vps_Config_Web::getInstance($webVersion);
            $sshHost = $config->server->user.'@'.$config->server->host;
            $sshDir = $config->server->dir;
            $cmd = "switch get-version";
            $cmd = "sshvps $sshHost $sshDir $cmd";
            $cmd = "sudo -u vps $cmd";
            if ($this->_getParam('debug')) echo $cmd."\n";
            exec($cmd, $out, $ret);
            if ($ret) {
                throw new Vps_ClientException("get-version failed");
            }
            $versions = unserialize(implode("", $out));
            if (!$versions) {
                throw new Vps_ClientException("get-verions failed");
            }
            $webVersion = $versions['web'];
            $vpsVersion = $versions['vps'];
        }
        $cmd = "php bootstrap.php tag-checkout web-switch --version=$webVersion";
        if ($this->_getParam('debug')) {
            $cmd .= " --debug";
            echo "$cmd\n";
        }
        $this->_systemCheckRet($cmd);

        if (!$vpsVersion || $vpsVersion == file_get_contents('application/vps_branch')) {
            echo "It's the vps_branch version, setting %vps_branch% in include_path\n";
            if ($path = $this->_getIncludePathBase()) {
                $path .= '%vps_branch%';
                file_put_contents('application/include_path', $path);
            } else {
                echo "Couldn't update application/include_path\n";
            }
        } else {
            echo "It's some custom vps version, using vps-tag\n";
            $svnPath = "http://svn/tags/vps/".$vpsVersion; //passt das immer?
            $vpsTagPath = preg_replace('#[^/]+$#', '', rtrim(trim(VPS_PATH), '/')).'vps-tag';
            if (!file_exists($vpsTagPath)) {
                echo "$vpsTagPath doesn't exist, initial checkout... (will take some time)\n";
                $cmd = "svn co $svnPath $vpsTagPath";
                copy(VPS_PATH.'/include_path', $vpsTagPath.'/include_path');
            } else {
                echo "switch $vpsTagPath to $vpsVersion...\n";
                $cmd = "svn sw $svnPath $vpsTagPath";
            }
            if ($this->_getParam('debug')) echo "$cmd\n";
            $this->_systemCheckRet("$cmd >/dev/null");
            file_put_contents('application/include_path', $vpsTagPath);
        }
        echo "Checked Out Web $webVersion with Vps $vpsVersion\n";
        echo "You should probably import\n";
        exit;
    }

    private function _getVersion($path)
    {
        $xml = new SimpleXMLElement(`svn info --xml $path`);
        if (!preg_match('#(trunk|branches|tags)/(.*)$#', (string)$xml->entry->url, $m)) {
            throw new Vps_Exception("Can't get version");
        }
        if ($m[1] == 'trunk') return 'trunk';
        if (!preg_match('#^[^/]+/([^/]+)$#', $m[2], $m)) {
            throw new Vps_Exception("Can't get version");
        }
        return $m[1];
    }

    public function getVersionAction()
    {
        $ret = array();
        $ret['web'] = $this->_getVersion(".");
        $ret['vps'] = $this->_getVersion(VPS_PATH);
        echo serialize($ret);
        exit;
    }
}
