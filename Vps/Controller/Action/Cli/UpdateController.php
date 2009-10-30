<?php
class Vps_Controller_Action_Cli_UpdateController extends Vps_Controller_Action_Cli_Abstract
{
    public static function getHelp()
    {
        return 'Update to current version';
    }
    public static function getHelpOptions()
    {
        return array(
            array(
                'param'=> 'current',
                'help' => 'Also execute updates for current revision'
            ),
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
        self::update($this->_getParam('rev'), $this->_getParam('current'),
            $this->_getParam('debug'), $this->_getParam('skip-clear-cache'));
        exit;
    }

    public static function update($rev = false, $current = false, $debug = false, $skipClearCache = false)
    {
        echo "Update\n";
        $currentRevision = false;
        try {
            $info = new SimpleXMLElement(`svn info --xml`);
            $currentRevision = (int)$info->entry['revision'];
        } catch (Exception $e) {}
        if (!$currentRevision) {
            throw new Vps_ClientException("Can't detect current revision");
        }

        if (!file_exists('application/update')) {
            file_put_contents('application/update', serialize(array('start' => $currentRevision)));
            echo "No application/update revision found, wrote current revision ($currentRevision)\n";
            exit;
        }
        $updateRevision = file_get_contents('application/update');
        if (is_numeric(trim($updateRevision))) {
            $updateRevision = array('start' => trim($updateRevision));
        } else {
            $updateRevision = unserialize($updateRevision);
        }
        if (!$updateRevision) {
            throw new Vps_ClientException("Invalid application/update revision");
        }
        if (!isset($updateRevision['done'])) $updateRevision['done'] = array();
        $from = $updateRevision['start'];
        $to = $currentRevision;
        if ($current) {
            $to++;
        } else if ($rev) {
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
        if ($from == $to) {
            echo "Already up-to-date\n\n";
        } else {
            echo "Looking for update-scripts from revision $from to {$to}...";
            $updates = Vps_Update::getUpdates($from, $to);
            foreach ($updates as $k=>$u) {
                if ($u->getRevision() && in_array($u->getRevision(), $updateRevision['done']) && !$rev) {
                    if ($current && $u->getRevision() == $to-1) continue;
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
                    if (!in_array($u->getRevision(), $updateRevision['done'])) {
                        $updateRevision['done'][] = $u->getRevision();
                    }
                }
                file_put_contents('application/update', serialize($updateRevision));
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
    }

    private static function _executeUpdate($updates, $method, $debug = false, $skipClearCache = false)
    {
        $ret = true;
        foreach ($updates as $update) {
            if ($update->getTags()) {
                if (!array_intersect(
                    $update->getTags(),
                    Vps_Registry::get('config')->server->updateTags->toArray()
                )) {
                    if ($method != 'checkSettings') {
                        echo "$method: skipping ".get_class($update);
                        if ($update->getRevision()) echo " (".$update->getRevision().")";
                        echo ", tags '".implode(', ', $update->getTags())."' don't match\n";
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
                echo "executing $method ".get_class($update);
                if ($update->getRevision()) echo " (".$update->getRevision().")";
                echo "... ";
            }
            $e = false;
            if (in_array('db', $update->getTags())) {
                $databases = array();
                foreach (Vps_Registry::get('config')->server->databases as $db) {
                    try {
                        Vps_Registry::get('dao')->getDbConfig($db);
                    } catch (Exception $e) {
                        continue;
                    }
                    $database[] = $db;
                }
            } else {
                $databases = array('db');
            }
            try {
                foreach ($databases as $db) {
                    Vps_Registry::set('db', Vps_Registry::get('dao')->getDb($db));
                    $res = $update->$method();
                }
                Vps_Registry::set('db', Vps_Registry::get('dao')->getDb());
            } catch (Exception $e) {
                if ($debug) throw $e;
                if ($method == 'checkSettings') {
                    echo get_class($update);
                }
                echo "\n\033[31mError:\033[0m\n";
                echo $e->getMessage()."\n\n";
                $ret = false;
            }
            if (!$e) {
                if ($method != 'checkSettings') echo "\033[32 OK \033[0m\n";
                if ($res) {
                    print_r($res);
                }
            }
        }
        return $ret;
    }
}
