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

        if ($this->_getParam('class')) {
            $update = Kwf_Util_Update_Helper::createUpdate($this->_getParam('class'));
            if (!$update) { echo 'could not create update.'; exit; }

            $runner = new Kwf_Util_Update_Runner(array($update));
            $runner->setEnableDebug($this->_getParam('debug'));
            $runner->setSkipClearCache($this->_getParam('skip-clear-cache'));
            $runner->executeUpdates(self::_getDoneNames());
        } else {
            $this->_update();
        }
        exit;
    }

    private function _update()
    {
        $rev = $this->_getParam('rev');
        $debug = $this->_getParam('debug');
        $skipClearCache = $this->_getParam('skip-clear-cache');

        ini_set('memory_limit', '512M');
        if (!$skipClearCache) {
            Kwf_Util_ClearCache::getInstance()->clearCache('all', false, false);
        }
        echo "Update\n";

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
        $doneNames = self::_getDoneNames();
        foreach ($updates as $k=>$u) {
            if ($u->getRevision() && in_array($u->getUniqueName(), $doneNames) && !$rev) {
                unset($updates[$k]);
            }
        }
        echo " found ".count($updates)."\n\n";
        $runner = new Kwf_Util_Update_Runner($updates);
        $runner->setEnableDebug($debug);
        $runner->setSkipClearCache($skipClearCache);
        $runner->executeUpdates($doneNames);
    }

    private static function _getDoneNames()
    {
        $db = Kwf_Registry::get('db');
        try {
            $q = $db->query("SELECT data FROM kwf_update");
        } catch (Exception $e) {
        }
        $doneNames = false;
        if (isset($q)) {
            $doneNames = $q->fetchColumn();
        }
        if (!$doneNames) {
            //fallback for older versions, update used to be a file
            if (!file_exists('update')) {
                $doneNames = array();
                foreach (Kwf_Util_Update_Helper::getUpdates(0, 9999999) as $u) {
                    $doneNames[] = $u->getUniqueName();
                }
                $db->query("UPDATE kwf_update SET data=?", serialize($doneNames));
                echo "No update revision found, assuming up-to-date\n";
                exit;
            }
            $doneNames = file_get_contents('update');
        }

        if (is_numeric(trim($doneNames))) {
            //UPDATE applicaton/update format
            $r = trim($doneNames);
            $doneNames = array();
            foreach (Kwf_Util_Update_Helper::getUpdates(0, $r) as $u) {
                $doneNames[] = $u->getUniqueName();
            }
        } else {
            $doneNames = unserialize($doneNames);
            if (isset($doneNames['start'])) {
                //UPDATE applicaton/update format
                if (!isset($doneNames['done'])) {
                    $doneNames['done'] = array();
                    foreach (Kwf_Util_Update_Helper::getUpdates(0, $doneNames['start']) as $u) {
                        $doneNames['done'][] = $u->getRevision();
                    }
                }
                $doneNames = $doneNames['done'];
            }
            $doneNamesCpy = $doneNames;
            $doneNames = array();
            foreach ($doneNamesCpy as $i) {
                if (is_numeric($i)) {
                    //UPDATE applicaton/update format
                    static $allUpdates;
                    if (!isset($allUpdates)) {
                        $allUpdates = array();
                        foreach (Kwf_Util_Update_Helper::getUpdates(0, 9999999) as $u) {
                            if (!isset($allUpdates[$u->getRevision()])) $allUpdates[$u->getRevision()] = array();
                            $allUpdates[$u->getRevision()][] = $u;
                        }
                    }
                    if (isset($allUpdates[$i])) {
                        foreach ($allUpdates[$i] as $u) {
                            $doneNames[] = $u->getUniqueName();
                        }
                    }
                } else {
                    $doneNames[] = $i;
                }
            }
        }

        //convert old updates from pre 3.0 times (where kwf was called vps)
        foreach ($doneNames as &$i) {
            if (substr($i, 0, 4) == 'Vpc_' || substr($i, 0, 4) == 'Vps_') {
                $updateWithoutWebname = substr($i, strpos($i, '_', 4)+1);
                if (class_exists($updateWithoutWebname)) {
                    $i = $updateWithoutWebname;
                    continue;
                }
                $updateSqlFile = str_replace('_', '/', $updateWithoutWebname).'.sql';
                foreach (explode(PATH_SEPARATOR, get_include_path()) as $ip) {
                    if (file_exists($ip.'/'.$updateSqlFile)) {
                        $i = $updateWithoutWebname;
                        continue;
                    }
                }

                $i = str_replace('Vps_Update_', 'Vkwf_Update_', $i);

                $i = str_replace('Vps_', 'Kwf_', $i);
                $i = str_replace('Vpc_', 'Kwc_', $i);
            }
        }

        if (!$doneNames) {
            //it's ok to have no updates throw new Kwf_ClientException("Invalid update revision");
        }
        return $doneNames;
    }
}
