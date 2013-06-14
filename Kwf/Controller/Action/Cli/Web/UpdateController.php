<?php
class Kwf_Controller_Action_Cli_Web_UpdateController extends Kwf_Controller_Action_Cli_Abstract
{
    public static function getHelp()
    {
        return 'Update to current version';
    }
    public static function getHelpOptions()
    {
        return array(
            array(
                'param'=> 'rev',
                'value' => '12300[:12305]',
                'allowBlank' => true,
                'help' => 'Executes update for a given revision'
            ),
            array(
                'param'=> 'class',
                'value' => 'Kwc_..._Update_2',
                'allowBlank' => true,
                'help' => 'Executes specific update (also .sql)'
            ),
            array(
                'param'=> 'skip-clear-cache',
                'help' => 'Don\'t clear cache after update'
            )
        );
    }
    public function indexAction()
    {
        ini_set('memory_limit', '512M');
        Kwf_Component_ModelObserver::getInstance()->disable();

        //try to update old-style db config
        if (file_exists('config.db.ini')) {
            $db = file_get_contents('config.db.ini');
            if (file_exists('config.local.ini')) {
                $c = file_get_contents('config.local.ini');
            } else {
                $c = "[production]\n";
            }
            $c .= "\n";
            $db = str_replace("[database]\n", '', $db);
            foreach (explode("\n", trim($db)) as $line) {
                if (trim($line)) $c .= "database.".$line."\n";
            }
            file_put_contents('config.local.ini', $c);
            unlink('config.db.ini');
        }

        $skipClearCache = $this->_getParam('skip-clear-cache');

        $doneNames = Kwf_Util_Update_Helper::getExecutedUpdatesNames();

        if ($this->_getParam('class')) {
            $update = Kwf_Util_Update_Helper::createUpdate($this->_getParam('class'));
            if (!$update) { echo 'could not create update.'; exit(1); }
            $updates = array($update);
        } else {
            $rev = $this->_getParam('rev');

            if (!$skipClearCache) {
                Kwf_Util_ClearCache::getInstance()->clearCache('all', false, false);
            }

            $from = 1;
            $to = 9999999;
            if ($rev) {
                $ex = explode(':', $rev, 2);
                $ex1 = $ex[0];
                if (!isset($ex[1])) {
                    $ex2 = null;
                } else {
                    $ex2 = $ex[1];
                }
                $from = $ex1;
                if (!$ex2) {
                    $to = $from + 1;
                } else if ($ex1 == $ex2) {
                    $to = $ex2 + 1;
                } else {
                    $to = $ex2;
                }
            }
            echo "Looking for update-scripts from revision $from to {$to}...";
            $updates = Kwf_Util_Update_Helper::getUpdates($from, $to);
            foreach ($updates as $k=>$u) {
                if ($u->getRevision() && in_array($u->getUniqueName(), $doneNames) && !$rev) {
                    unset($updates[$k]);
                }
            }
            echo " found ".count($updates)."\n\n";
        }


        if (!$this->_getParam('debug')) Kwf_Util_Maintenance::writeMaintenanceBootstrap();

        $c = new Zend_ProgressBar_Adapter_Console();
        $c->setElements(array(Zend_ProgressBar_Adapter_Console::ELEMENT_PERCENT,
                                Zend_ProgressBar_Adapter_Console::ELEMENT_BAR,
                                Zend_ProgressBar_Adapter_Console::ELEMENT_TEXT));
        $c->setTextWidth(50);

        $runner = new Kwf_Util_Update_Runner($updates);
        $progress = new Zend_ProgressBar($c, 0, $runner->getProgressSteps());
        $runner->setProgressBar($progress);
        $runner->setVerbose(true);
        $runner->setEnableDebug($this->_getParam('debug'));
        $runner->setSkipClearCache($skipClearCache);
        if (!$runner->checkUpdatesSettings()) {
            echo "\ncheckSettings failed, update stopped\n";
        } else {
            $executedUpdates = $runner->executeUpdates();
            echo "\n\033[32mupdate finished\033[0m\n";
            $doneNames = array_unique(array_merge($doneNames, $executedUpdates));
            $runner->writeExecutedUpdates($doneNames);
        }
        
        if (!$this->_getParam('debug')) Kwf_Util_Maintenance::restoreMaintenanceBootstrap();

        $errors = $runner->getErrors();
        if ($errors) {
            echo "\n\n================\n";
            echo count($errors)." update script(s) failed:\n";
            foreach ($errors as $error) {
                echo $error['name'].": \n";
                echo $error['message']."\n\n";
            }
            exit(1);
        } else {
            echo "\n".count($updates)." update script(s) successfully executed.\n";
            exit(0);
        }
    }
}
