<?php
class Vps_Controller_Action_Cli_CommitAndMergeController extends Vps_Controller_Action_Cli_Abstract
{
    public static function getHelp()
    {
        return "commit to tag and merge to trunk";
    }

    public static function getHelpOptions()
    {
        return array(
            array(
                'param'=> 'message',
            ),
            array(
                'param'=> 'record-only',
            )
        );
    }

    private function _svnStat($dir)
    {
        exec("svn info $dir", $out, $ret);
        if ($ret) return false;
        return true;
    }

    public function indexAction()
    {
        $message = $this->_getParam('message');
        if (!$message) throw new Vps_ClientException("--message is required");

        $xml = new SimpleXMLElement(`svn info --xml`);
        $tagUrl = (string)$xml->entry->url;
        if (!preg_match('#^(.*)(trunk|branches|tags)/([^/]+)#', $tagUrl, $m)) {
            throw new Vps_Exception("invalid url");
        }
        if ($m[2] == 'trunk') {
            throw new Vps_ClientException("Can't use this script while on trunk");
        }

        if ($this->_svnStat('http://svn/trunk/vps-projekte/'.$m[3])) {
            $trunkUrl = $m[1].'trunk/vps-projekte/'.$m[3];
        } else if ($this->_svnStat('http://svn/trunk/vw-projekte/'.$m[3])) {
            $trunkUrl = $m[1].'trunk/vw-projekte/'.$m[3];
        } else {
            throw new Vps_Exception("Can't figure out trunk url");
        }

        $cmd = "svn ci -m ".escapeshellarg($message);
        if ($this->_getParam('debug')) echo $cmd."\n";
        $revision = system($cmd, $ret);
        if ($ret) {
            throw new Vps_ClientException("Commit failed");
        }
        if (!preg_match('#[0-9]+#', $revision, $m)) {
            throw new Vps_ClientException("can't figure out revision");
        }
        $revision = $m[0];

        $cmd = "svn sw $trunkUrl";
        echo "\n$cmd\n";
        $this->_systemCheckRet("$cmd");

        $cmd = "svn merge";
        if ($this->_getParam('record-only')) {
            $cmd .= " --record-only";
        }
        $cmd .= " -c ".$revision;
        $cmd .= " $tagUrl";
        echo "\n$cmd\n";
        $this->_systemCheckRet("$cmd");

        $cmd = "svn ci -m ".escapeshellarg("merged: $message");
        echo "\n$cmd\n";
        $this->_systemCheckRet("$cmd");

        $cmd = "svn sw $tagUrl";
        echo "\n$cmd\n";
        $this->_systemCheckRet("$cmd");

        echo "\ndone.\n";
        exit;
    }
}
