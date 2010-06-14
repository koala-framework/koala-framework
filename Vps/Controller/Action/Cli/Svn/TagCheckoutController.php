<?php
class Vps_Controller_Action_Cli_Svn_TagCheckoutController extends Vps_Controller_Action_Cli_Abstract
{
    public static function getHelp()
    {
        if (!file_exists('.svn')) return null;
        //if (Vps_Registry::get('config')->server->host == 'vivid') return null;
        return "checkout vps tag";
    }

    public static function getHelpOptions()
    {
        return array(
            array(
                'param'=> 'version',
                'value' => 'vps version',
                'allowBlank'=>false,
            )
        );
    }

    public function indexAction()
    {
        throw new Vps_ClientException("nothing to see here, just used by go-online");
    }

    public function vpsCheckoutAction()
    {
        $vpsPath = VPS_PATH;

        $path = substr($vpsPath, 0, strrpos($vpsPath, '/'));
        $path .= '/'.$this->_getParam('version');

        if (file_exists($path)) {
            echo "$path exists already.\nupadting...\n";
            $this->_systemCheckRet("svn up $path");
        } else {

            $url = false;
            try {
                $vpsPath = realpath($vpsPath);
                $info = new SimpleXMLElement(`svn info --xml $vpsPath`);
                $url = (string)$info->entry->url;
            } catch (Exception $e) {}
            if (!$url) {
                throw new Vps_Exception("Can't detect svn url");
            }
            if (substr($url, -10) == '/trunk/vps') {
                $url = substr($url, 0, -10);
            } else if (preg_match('#^(.+)/(tags|branches)/vps/[^/]+$#', $url, $m)) {
                $url = $m[1];
            } else {
                throw new Vps_Exception("Can't detect vps base url from '$url'");
            }
            if ($this->_getParam('version') == 'trunk') {
                $url .= "/trunk/vps";
            } else {
                $url .= "/tags/vps/".$this->_getParam('version');
            }

            $this->_systemCheckRet("svn co $url $path");

            $ip = file_get_contents("$vpsPath/include_path");
            echo "include_path: $ip\n";
            file_put_contents($path.'/include_path', $ip);
        }

        $this->_helper->viewRenderer->setNoRender(true);
    }

    public function vpsUseAction()
    {
        if (!file_exists("application/include_path")) {
            throw new Vps_ClientException("stange, application/include_path does not exist");
        }
        $path = substr(VPS_PATH, 0, strrpos(VPS_PATH, '/'));
        if ($this->_getParam('version') == 'branch') {
            $path .= '/%vps_branch%';
        } else {
            $path .= '/'.$this->_getParam('version');
            if (!file_exists($path)) {
                throw new Vps_ClientException("Path '$path' does not exist");
            }
        }
        file_put_contents('application/include_path', $path);
        echo "switched to vps version ".$this->_getParam('version')."\n";

        $this->_helper->viewRenderer->setNoRender(true);
    }

    private static function _svnStat($dir)
    {
        exec("svn ls $dir", $out, $ret);
        if ($ret) return false;
        return true;
    }

    public static function getWebSvnPathForVersion($version)
    {
        $xml = new SimpleXMLElement(`svn info --xml`);
        $currentUrl = (string)$xml->entry->url;
        if (!preg_match('#^(.*)(trunk|branches|tags)/([^/]+)#', $currentUrl, $m)) {
            throw new Vps_Exception("invalid url");
        }
        if (preg_match('#^(.+)/trunk/(vps|vw)-projekte/([^/]+)$#', $currentUrl, $m)) {
            $project = $m[3];
            $baseUrl = $m[1];
        } else if (preg_match('#^(.+)/tags/([^/]+)/[^/]+$#', $currentUrl, $m)) {
            $project = $m[2];
            $baseUrl = $m[1];
        } else {
            throw new Vps_Exception("Can't detect web base url from $currentUrl");
        }

        if ($version == 'trunk') {
            if (self::_svnStat($baseUrl.'/trunk/vps-projekte/'.$project)) {
                return $baseUrl.'/trunk/vps-projekte/'.$project;
            } else if (self::_svnStat($baseUrl.'/trunk/vw-projekte/'.$project)) {
                return $baseUrl.'/trunk/vw-projekte/'.$project;
            } else {
                throw new Vps_Exception("Can't figure out trunk url");
            }
        } else {
            return $baseUrl. "/tags/$project/".$version;
        }
    }

    public function webSwitchAction()
    {
        $url = self::getWebSvnPathForVersion($this->_getParam('version'));

        echo "updating web to ".$this->_getParam('version')."\n";
        $this->_systemCheckRet("svn sw $url");

        $this->_helper->viewRenderer->setNoRender(true);
    }
}
