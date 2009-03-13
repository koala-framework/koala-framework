<?php
class Vps_Controller_Action_Cli_SvnUpController extends Vps_Controller_Action_Cli_Abstract
{
    public static function getHelp()
    {
        return "svn update web+vps";
    }

    public function indexAction()
    {
        echo "updating web\n";
        passthru('svn up');
        echo "\nupdating vps\n";
        passthru('svn up '.VPS_PATH);

        $projectIds = array();
        if (Vps_Registry::get('config')->todo->projectIds) {
            $projectIds = Vps_Registry::get('config')->todo->projectIds->toArray();
        }
        if ($projectIds && Vps_Registry::get('config')->todo->markAsOnTestOnUpdate) {
            $m = Vps_Model_Abstract::getInstance('Vps_Util_Model_Todo');
            $s = $m->select()
                    ->whereEquals('project_id', $projectIds)
                    ->whereEquals('status', 'committed');
            $doneTodos = $m->getRows($s);
            foreach ($doneTodos as $todo) {
                if (!$todo->done_revision) continue;
                if ($this->_hasRevisionInHistory('.', $todo->done_revision)
                    || $this->_hasRevisionInHistory(VPS_PATH, $todo->done_revision)
                ) {
                    $todo->status = 'test';
                    $todo->test_date = date('Y-m-d');
                    $todo->save();
                    echo "\ntodo #{$todo->id} ({$todo->title}) als auf test markiert";
                }
            }
        }

        echo "\n";
        $this->_forward('index', 'update');

        $this->_helper->viewRenderer->setNoRender(true);
    }
    private function _hasRevisionInHistory($path, $revision)
    {
        $log = `svn log --xml --revision $revision $path`;
        $log = new SimpleXMLElement($log);
        if (count($log->logentry)) return true;
    }
    public function checkForModifiedFilesAction()
    {
        self::checkForModifiedFiles();
        $this->_helper->viewRenderer->setNoRender(true);
    }

    public static function checkForModifiedFiles()
    {
        self::_check('');
        echo "Web OK\n";
        self::_check(VPS_PATH);
        echo "Vps OK\n";
    }

    private static function _check($path)
    {
        exec('svn st --xml '.$path, $out, $ret);
        if ($ret) {
            throw new Vps_ClientException("Failed checking for modified files");
        }
        $xml = new SimpleXMLElement(implode('', $out));
        if (!$xml) {
            throw new Vps_ClientException("Failed checking for modified files");
        }
        $files = array();
        foreach ($xml->target->entry as $e) {
            $files[(string)$e->{'wc-status'}['item']][] = (string)$e['path'];
        }
        if ($files) {
            echo "working copy contains ";
            foreach ($files as $status=>$f) {
                echo count($f)." $status ";
            }
            echo "files\n";
            if ($path == '') $path = getcwd();
            throw new Vps_ClientException("You must not have modified files in '$path'");
        }
    }
}
