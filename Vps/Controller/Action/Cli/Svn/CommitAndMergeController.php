<?php
class Vps_Controller_Action_Cli_Svn_CommitAndMergeController extends Vps_Controller_Action_Cli_Abstract
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

    public function indexAction()
    {
        if (!file_exists('.svn')) {
            throw new Vps_ClientException("This script is not yet ported to git");
        }
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

        $trunkUrl = Vps_Controller_Action_Cli_Svn_TagCheckoutController::getWebSvnPathForVersion('trunk');

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
