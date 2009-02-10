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
            )
        );
    }
    public function indexAction()
    {
        self::update($this->_getParam('rev'), $this->_getParam('current'));
        exit;
    }

    public static function update($rev = false, $current = false)
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
            echo "Looking for update-scripts from revistion $from to {$to}...";
            $updates = Vps_Update::getUpdates($from, $to);
            foreach ($updates as $k=>$u) {
                if (in_array($u->getRevision(), $updateRevision['done']) && !$rev) {
                    if ($current && $u->getRevision() == $to-1) continue;
                    unset($updates[$k]);
                }
            }
            echo " found ".count($updates)."\n\n";

            if (self::_executeUpdate($updates, 'checkSettings')) {

                $offlineBootstrap  = "<?php\nheader(\"HTTP/1.0 503 Service Unavailable\");\n";
                $offlineBootstrap .= "echo \"<html><head><title>503 Service Unavailable</title></head><body>\";\n";
                $offlineBootstrap .= "echo \"<h1>Service Unavailable</h1>\";\n";
                $offlineBootstrap .= "echo \"<p>Server ist im Moment wegen Wartungsarbeiten nicht verfügbar.</p>\";\n";
                $offlineBootstrap .= "echo \"</body></html>\";\n";
                if (!file_exists('bootstrap.php.backup')) {
                    rename('bootstrap.php', 'bootstrap.php.backup');
                    file_put_contents('bootstrap.php', $offlineBootstrap);
                    echo "\nwrote offline bootstrap.php";
                }

                self::_executeUpdate($updates, 'preUpdate');
                self::_executeUpdate($updates, 'update');
                self::_executeUpdate($updates, 'postUpdate');
                echo "\ncleared cache";
                foreach ($updates as $k=>$u) {
                    if (!in_array($u->getRevision(), $updateRevision['done'])) {
                        $updateRevision['done'][] = $u->getRevision();
                    }
                }
                file_put_contents('application/update', serialize($updateRevision));
                echo "\n\033[32mupdate finished\033[0m\n";

                if (file_get_contents('bootstrap.php') == $offlineBootstrap) {
                    rename('bootstrap.php.backup', 'bootstrap.php');
                    echo "\nrestored bootstrap.php\n";
                }

            } else {
                echo "\nupdate stopped\n";
            }
        }
    }

    private static function _executeUpdate($updates, $method)
    {
        $ret = true;
        foreach ($updates as $update) {
            if ($method != 'checkSettings') {
                Vps_Controller_Action_Cli_ClearCacheController::clearCache();
                Zend_Db_Table_Abstract::getDefaultMetadataCache()->clean(); //just to be save
                Vps_Model_Abstract::clearInstances(); //wegen eventueller meta-data-caches die sich geändert haben
                echo "executing $method ".get_class($update)."... ";
            }
            $e = false;
            try {
                $res = $update->$method();
            } catch (Exception $e) {
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
