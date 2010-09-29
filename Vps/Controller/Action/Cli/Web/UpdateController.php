<?php
class Vps_Controller_Action_Cli_Web_UpdateController extends Vps_Controller_Action_Cli_Abstract
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
                'param'=> 'skip-clear-cache',
                'help' => 'Don\'t clear cache after update'
            )
        );
    }
    public function indexAction()
    {
        self::update($this->_getParam('rev'), $this->_getParam('debug'), $this->_getParam('skip-clear-cache'));
        exit;
    }

    public static function update($rev = false, $debug = false, $skipClearCache = false)
    {
        if (!$skipClearCache) {
            Vps_Util_ClearCache::getInstance()->clearCache('all', false, false);
        }
        echo "Update\n";

        if (in_array('vps', Vps_Registry::get('config')->server->updateTags->toArray())) {
            if (!file_exists('.git') && Vps_Registry::get('config')->application->id!='zeiterfassung') {
                echo "\n\n\n\n!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!\n";
                echo "ACHTUNG web (und eventuell vps) wurden auf git umgestellt.\n";
                system("php bootstrap.php git convert-to-git", $ret);
                if ($ret) {
                    throw new Vps_ClientException("Git konvertierung fehlgeschlagen! Bitte manuell konvertieren.");
                }
            }
        }

        if (!file_exists('application/update')) {
            $doneNames = array();
            foreach (Vps_Update::getUpdates(0, 9999999) as $u) {
                $doneNames[] = $u->getUniqueName();
            }
            file_put_contents('application/update', serialize($doneNames));
            echo "No application/update revision found, assuming up-to-date\n";
            exit;
        }
        $doneNames = file_get_contents('application/update');
        if (is_numeric(trim($doneNames))) {
            //UPDATE applicaton/update format
            $r = trim($doneNames);
            $doneNames = array();
            foreach (Vps_Update::getUpdates(0, $r) as $u) {
                $doneNames[] = $u->getUniqueName();
            }
        } else {
            $doneNames = unserialize($doneNames);
            if (isset($doneNames['start'])) {
                //UPDATE applicaton/update format
                if (!isset($doneNames['done'])) $doneNames['done'] = array();
                foreach (Vps_Update::getUpdates(0, $doneNames['start']) as $u) {
                    $doneNames['done'][] = $u->getRevision();
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
                        foreach (Vps_Update::getUpdates(0, 9999999) as $u) {
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
        if (!$doneNames) {
            throw new Vps_ClientException("Invalid application/update revision");
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
        $updates = Vps_Update::getUpdates($from, $to);
        foreach ($updates as $k=>$u) {
            if ($u->getRevision() && in_array($u->getUniqueName(), $doneNames) && !$rev) {
                unset($updates[$k]);
            }
        }
        echo " found ".count($updates)."\n\n";

        if (self::_executeUpdate($updates, 'checkSettings', $debug, $skipClearCache)) {

            if (!$debug && Zend_Registry::get('config')->whileUpdatingShowMaintenancePage) {
                $offlineBootstrap  = "<?php\nheader(\"HTTP/1.0 503 Service Unavailable\");\n";
                $offlineBootstrap .= "echo \"<html><head><title>503 Service Unavailable</title></head><body>\";\n";
                $offlineBootstrap .= "echo \"<h1>Service Unavailable</h1>\";\n";
                $offlineBootstrap .= "echo \"<p>Server ist im Moment wegen Wartungsarbeiten nicht verfügbar.</p>\";\n";
                $offlineBootstrap .= "echo \"</body></html>\";\n";
                if (!file_exists('bootstrap.php.backup')) {
                    rename('bootstrap.php', 'bootstrap.php.backup');
                    file_put_contents('bootstrap.php', $offlineBootstrap);
                    echo "\nwrote offline bootstrap.php\n\n";
                }
            }

            self::_executeUpdate($updates, 'preUpdate', $debug, $skipClearCache);
            self::_executeUpdate($updates, 'update', $debug, $skipClearCache);
            self::_executeUpdate($updates, 'postUpdate', $debug, $skipClearCache);
            if (!$skipClearCache) {
                echo "\n";
                Vps_Util_ClearCache::getInstance()->clearCache('all', true);
                echo "\n";
            }
            self::_executeUpdate($updates, 'postClearCache', $debug, $skipClearCache);
            foreach ($updates as $k=>$u) {
                if (!in_array($u->getRevision(), $doneNames)) {
                    $doneNames[] = $u->getUniqueName();
                }
            }
            file_put_contents('application/update', serialize($doneNames));
            echo "\n\033[32mupdate finished\033[0m\n";

            if (!$debug && Zend_Registry::get('config')->whileUpdatingShowMaintenancePage) {
                if (file_get_contents('bootstrap.php') == $offlineBootstrap) {
                    rename('bootstrap.php.backup', 'bootstrap.php');
                    echo "\nrestored bootstrap.php\n";
                }
            }

        } else {
            echo "\nupdate stopped\n";
        }
    }

    private static function _executeUpdate($updates, $method, $debug = false, $skipClearCache = false)
    {
        $ret = true;
        foreach ($updates as $update) {
            if ($update->getTags() && !in_array('web', $update->getTags())) {
                if (!array_intersect(
                    $update->getTags(),
                    Vps_Registry::get('config')->server->updateTags->toArray()
                ) && !($update->getTags()==array('db') && get_class($update)=='Vps_Update_Sql')) {
                    if ($method != 'checkSettings') {
                        echo "$method: skipping ".get_class($update);
                        if ($update->getRevision()) echo " (".$update->getRevision().")";
                        echo ", tags '".implode(', ', $update->getTags())."' don't match ";
                        echo "(".implode(', ', Vps_Registry::get('config')->server->updateTags->toArray()).")";
                        echo "\n";
                        flush();
                    }
                    continue; //skip
                }
            }
            if ($method != 'checkSettings') {
                if ($method != 'postClearCache' && !$skipClearCache) {
                    Vps_Util_ClearCache::getInstance()->clearCache('all', false, false);
                }
                Vps_Model_Abstract::clearInstances(); //wegen eventueller meta-data-caches die sich geändert haben
                Vps_Component_Generator_Abstract::clearInstances();
                Vps_Component_Data_Root::reset();
                echo "\nexecuting $method ".$update->getUniqueName();
                echo "... ";
                flush();
            }
            $e = false;
            if (in_array('db', $update->getTags())) {
                $databases = Vps_Registry::get('config')->server->databases->toArray();
            } else {
                $databases = array('web');
            }
            foreach ($databases as $db) {
                if (!$db) continue;
                if ($method != 'checkSettings') {
                    echo $db.' ';
                    flush();
                }
                try {
                    if (Vps_Registry::get('dao')) {
                        $db = Vps_Registry::get('dao')->getDb($db);
                    } else {
                        $db = null;
                    }
                } catch (Exception $e) {
                    echo "skipping, invalid db\n";
                    flush();
                    continue;
                }
                Vps_Registry::set('db', $db);
                try {
                    $res = $update->$method();
                } catch (Exception $e) {
                    if ($debug) throw $e;
                    if ($method == 'checkSettings') {
                        echo get_class($update);
                    }
                    echo "\n\033[31mError:\033[0m\n";
                    echo $e->getMessage()."\n\n";
                    flush();
                    $ret = false;
                }
                if (!$e) {
                    if ($res) {
                        print_r($res);
                    }
                }

                //reset to default database
                $db = null;
                try {
                    if (Vps_Registry::get('dao')) $db = Vps_Registry::get('dao')->getDb();
                } catch (Exception $e) {}
                Vps_Registry::set('db', $db);
            }
            if ($method != 'checkSettings' && $ret) {
                echo "\033[32 OK \033[0m\n";
            }
            flush();
        }
        return $ret;
    }
}
