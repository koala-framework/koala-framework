<?php
class Kwf_Controller_Action_Cli_GitController extends Kwf_Controller_Action_Cli_Abstract
{
    public static function getHelp()
    {
        return 'various git helpers';
    }

    public function preDispatch()
    {
        if ($this->_getParam('debug')) Kwf_Util_Git::setDebugOutput(true);
        parent::preDispatch();
    }

    public function checkoutStagingAction()
    {
        if (!$this->_getParam('revWeb') || !$this->_getParam('revKwf')) {
            throw new Kwf_ClientException("revWeb and revKwf parameters required");
        }

        Kwf_Util_Git::kwf()->fetch();
        Kwf_Util_Git::web()->fetch();

        Kwf_Util_Git::web()->checkout($this->_getParam('revWeb'));
        Kwf_Util_Git::kwf()->checkout($this->_getParam('revKwf'));
        exit;
    }

    public function checkoutMasterAction()
    {
        Kwf_Util_Git::kwf()->fetch();
        Kwf_Util_Git::web()->fetch();

        Kwf_Util_Git::kwf()->checkout(trim(file_get_contents('kwf_branch')));
        $appId = Kwf_Registry::get('config')->application->id;
        Kwf_Util_Git::web()->checkout("master");
        exit;
    }

    public function checkoutProductionAction()
    {
        Kwf_Util_Git::kwf()->fetch();
        Kwf_Util_Git::web()->fetch();

        //die werden von go-online raufkopiert
        Kwf_Util_Git::kwf()->system("checkout ".escapeshellarg('Kwf/Util/Git.php'));
        Kwf_Util_Git::kwf()->system("checkout ".escapeshellarg('Kwf/Controller/Action/Cli/GitController.php'));
        $appId = Kwf_Registry::get('config')->application->id;
        if (!Kwf_Util_Git::kwf()->revParse("production/$appId")) {
            Kwf_Util_Git::kwf()->checkoutBranch("production/$appId", "origin/production/$appId", '--track');
        }
        if (Kwf_Util_Git::kwf()->getActiveBranch() != "production/$appId") {
            Kwf_Util_Git::kwf()->checkout("production/$appId");
        }
        Kwf_Util_Git::kwf()->system("rebase origin/production/$appId");

        if (!Kwf_Util_Git::web()->revParse("production")) {
            Kwf_Util_Git::web()->checkoutBranch("production", "origin/production", '--track');
        }
        if (Kwf_Util_Git::web()->getActiveBranch() != "production") {
            Kwf_Util_Git::web()->checkout("production");
        }
        Kwf_Util_Git::web()->system("rebase origin/production");

        system("php bootstrap.php update", $ret);
        exit($ret);
    }

    public function upAction()
    {
        $this->updateAction();
    }

    public function updateAction()
    {
        $doUpdate = true;

        $previousKwfBranch = trim(file_get_contents('kwf_branch'));

        Kwf_Util_Git::web()->fetch();
        if (Kwf_Util_Git::web()->getActiveBranch() == 'master') {
            try {
                Kwf_Util_Git::web()->system("rebase origin/master");
            } catch (Kwf_Exception $e) {
                exit(1);
            }
        } else if (Kwf_Util_Git::web()->getActiveBranch() == 'production') {
            try {
                Kwf_Util_Git::web()->system("rebase origin/production");
            } catch (Kwf_Exception $e) {
                exit(1);
            }
        } else {
            echo "web: ".Kwf_Util_Git::web()->getActiveBranch()." != master, daher wird kein autom. rebase ausgefuehrt.\n";
            $doUpdate = false;
        }

        //neu laden, da er sich geaendert haben kann
        if (file_exists('include_path')) {
            $vp = str_replace('%kwf_branch%', trim(file_get_contents('kwf_branch')), trim(file_get_contents('include_path')));
        } else {
            $vp = getcwd().'/kwf-lib';
        }
        $g = new Kwf_Util_Git($vp);
        $g->fetch();
        $kwfBranch = trim(file_get_contents('kwf_branch'));
        if ($previousKwfBranch != $kwfBranch) {
            echo "kwf: web hat kwf_branch von $previousKwfBranch auf $kwfBranch geaendert;\n";
            if ($g->getActiveBranch() == $previousKwfBranch) {
                if (in_array($kwfBranch, $g->getBranches())) {
                    try {
                        $g->checkout($kwfBranch);
                    } catch (Kwf_Exception $e) {
                        exit(1);
                    }
                } else {
                    try {
                        $g->checkoutBranch($kwfBranch, 'origin/'.$kwfBranch);
                    } catch (Kwf_Exception $e) {
                        exit(1);
                    }
                }
            } else {
                echo "kwf: ".$g->getActiveBranch()." != $previousKwfBranch, daher wird kein autom. checkout ausgefuehrt.\n";
            }
        } else if ($g->getActiveBranch() == $kwfBranch) {
            try {
                $g->system("rebase origin/$kwfBranch");
            } catch (Kwf_Exception $e) {
                exit(1);
            }
        } else if ($g->getActiveBranch() == 'production/'.Kwf_Registry::get('config')->application->id) {
            try {
                $g->system("rebase origin/production/".Kwf_Registry::get('config')->application->id);
            } catch (Kwf_Exception $e) {
                exit(1);
            }
        } else {
            echo "kwf: ".$g->getActiveBranch()." != $kwfBranch, daher wird kein autom. rebase ausgefuehrt.\n";
        }

        if ($this->_getParam('with-library')) {
            echo "\nupdating library\n";
            $git = new Kwf_Util_Git(Kwf_Registry::get('config')->libraryPath);
            $git->system("pull --rebase");
        } else {
            echo "\n\033[01;33mlibrary skipped\033[00m: use --with-library if you wish to update library as well\n";
        }

        echo "\nMark completed Todos when on Test (switch on in config with \"todo.markAsOnTestOnUpdate = false\"): ";
        if (Kwf_Registry::get('config')->todo->markAsOnTestOnUpdate) {
            $projectIds = Kwf_Model_Abstract::getInstance('Kwf_Util_Model_Projects')
                ->getApplicationProjectIds();
            if ($projectIds) {
                $m = Kwf_Model_Abstract::getInstance('Kwf_Util_Model_Todo');
                $s = $m->select()
                        ->whereEquals('project_id', $projectIds)
                        ->whereEquals('status', 'committed');
                $doneTodos = $m->getRows($s);
                foreach ($doneTodos as $todo) {
                    if (!$todo->done_revision) continue;
                    if ($this->_hasRevisionInHistory($todo->done_revision)) {
                        $todo->status = 'test';
                        $todo->test_date = date('Y-m-d');
                        $todo->save();
                        echo "\ntodo #{$todo->id} ({$todo->title}) als auf test markiert";
                    }
                }
            }
            echo "ok.";
        } else {
            echo "skipped.";
        }
        echo "\n";

        echo "\n";
        if ($this->_getParam('skip-update')) {
            echo "\n\033[01;33mupdate skipped\033[00m\n";
        } else {
            if ($doUpdate) {
                system("php bootstrap.php update", $ret);
                exit($ret);
            } else {
                echo "Updates wurden NICHT ausgefuehrt.\n";
            }
        }

        exit;
    }

    private function _hasRevisionInHistory($revision)
    {
        return Kwf_Util_Git::web()->getActiveBranchContains($revision)
                || Kwf_Util_Git::kwf()->getActiveBranchContains($revision);
    }
}
